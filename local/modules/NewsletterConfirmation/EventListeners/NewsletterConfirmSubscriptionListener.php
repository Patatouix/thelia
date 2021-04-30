<?php

namespace NewsletterConfirmation\EventListeners;

use NewsletterConfirmation\Model\Base\NewsletterConfirmationQuery;
use NewsletterConfirmation\Model\NewsletterConfirmation as ModelNewsletterConfirmation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Newsletter\NewsletterEvent;
use Thelia\Mailer\MailerFactory;
use Thelia\Tools\TokenProvider;
use Thelia\Model\ConfigQuery;

/**
 * Class AreaDeletedListener
 * @package AreaDeletedListener\EventListener
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class NewsletterConfirmSubscriptionListener implements EventSubscriberInterface
{
    protected $mailer;
    protected $tokenProvider;

    public function __construct(MailerFactory $mailer, TokenProvider $tokenProvider)
    {
        $this->mailer = $mailer;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * @param AreaDeleteEvent $event
     */
    public function verifyEmail(NewsletterEvent $event)
    {
        $newsletter = $event->getNewsletter();

        if ($newsletter->getUnsubscribed()) {
            $newsletter
                ->setUnsubscribed(false)
                ->save();
        } else {
            //prevents email subscription confirmation from being sent
            $event->stopPropagation();

            //unsubscribe user
            $newsletter
                ->setUnsubscribed(true)
                ->save();

            //generate token
            $token = $this->tokenProvider->getToken();

            //store token in newsletter_confirmation table
            if (null === $newsletterConfirmation = NewsletterConfirmationQuery::create()->findOneByNewsletterId($newsletter->getId())) {
                $newsletterConfirmation = new ModelNewsletterConfirmation();
            }

            $newsletterConfirmation
                ->setConfirmationToken($token)
                ->setNewsletterId($newsletter->getId())
                ->save();

            //send verification email
            $this->mailer->sendEmailMessage(
                'newsletter_email_confirmation',
                [ ConfigQuery::getStoreEmail() => ConfigQuery::getStoreName() ],
                [ $event->getEmail() => $event->getFirstname()." ".$event->getLastname() ],
                [
                    'email' => $event->getEmail(),
                    'firstname' => $event->getFirstname(),
                    'lastname' => $event->getLastname(),
                    'id' => $newsletterConfirmation->getId(),
                    'token' => $token
                ],
                $event->getLocale()
            );
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::NEWSLETTER_CONFIRM_SUBSCRIPTION => [
                'verifyEmail', 256
            ]
        ];
    }
}