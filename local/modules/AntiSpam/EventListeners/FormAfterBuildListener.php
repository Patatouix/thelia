<?php

namespace AntiSpam\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Mailer\MailerFactory;
use Thelia\Tools\TokenProvider;
use Thelia\Model\ConfigQuery;


/**
 * [Description FormAfterBuildListener]
 */
class FormAfterBuildListener implements EventSubscriberInterface
{
    const FORM = 'thelia_contact';

    //protected $mailer;
    //protected $tokenProvider;

    /*public function __construct(MailerFactory $mailer, TokenProvider $tokenProvider)
    {
        //$this->mailer = $mailer;
        //$this->tokenProvider = $tokenProvider;
    }*/

    /**
     * @param AreaDeleteEvent $event
     */
    public function addAntiSpamFields(TheliaFormEvent $event)
    {
        $event->getForm()->getFormBuilder()
            ->add('honey_field', 'hidden')
            ->add('test_field', 'text');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            (TheliaEvents::FORM_AFTER_BUILD.".".self::FORM) => [
                'addAntiSpamFields', 128
            ]
        ];
    }
}