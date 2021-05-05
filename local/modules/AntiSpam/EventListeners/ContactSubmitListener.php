<?php

namespace AntiSpam\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Contact\ContactEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Mailer\MailerFactory;
use Thelia\Tools\TokenProvider;
use Thelia\Model\ConfigQuery;


/**
 * [Description FormAfterBuildListener]
 */
class ContactSubmitListener implements EventSubscriberInterface
{
    //const FORM = 'thelia_contact';

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
    public function checkSubmit(ContactEvent $event)
    {
        var_dump($event->form);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            (TheliaEvents::CONTACT_SUBMIT) => [
                'checkSubmit', 128
            ]
        ];
    }
}