<?php

namespace NewsletterConfirmation\EventListeners;

use NewsletterConfirmation\NewsletterConfirmation;
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
class NewsletterSubscribeListener implements EventSubscriberInterface
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
    public function updateConfig(NewsletterEvent $event)
    {
        if ($data = NewsletterConfirmation::getConfigValue('newsletter_email_confirmation', 1)) {

            //unsubscribe user
            $newsletter = $event->getNewsletter();
            $newsletter
                ->setUnsubscribed(true)
                ->save();

            //generate token
            $token = $this->tokenProvider->getToken();

            //store token in newsletter_confirmation table
            $newsletterConfirmation = new ModelNewsletterConfirmation();
            $newsletterConfirmation
                ->setConfirmationToken($token)
                ->setNewsletterId($newsletter->getId())
                ->save();

            $this->mailer->sendEmailMessage(
                'newsletter_email_confirmation',
                [ ConfigQuery::getStoreEmail() => ConfigQuery::getStoreName() ],
                [ $event->getEmail() => $event->getFirstname()." ".$event->getLastname() ],
                [
                    //'email' => $event->getEmail(),
                    //'firstname' => $event->getFirstname(),
                    //'lastname' => $event->getLastname(),
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
            TheliaEvents::NEWSLETTER_SUBSCRIBE => [
                'updateConfig', 128
            ]
        ];
    }
}