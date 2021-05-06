<?php

namespace AntiSpam\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Contact\ContactEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Mailer\MailerFactory;
use Thelia\Tools\TokenProvider;
use Thelia\Model\ConfigQuery;


/**
 * [Description FormAfterBuildListener]
 */
class ContactSubmitListener implements EventSubscriberInterface
{
    const FORM_FILLING_MINIMAL_TIME = 3000;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param AreaDeleteEvent $event
     */
    public function checkSubmit(ContactEvent $event)
    {
        $isSpam = false;
        $data = $event->getForm()->getData();

        //honeypot
        if (null !== $data['website']) {
            $isSpam = true;
        }

        //question
        if ($this->request->getSession()->get('questionAnswer') !== $data['questionAnswer']) {
            $isSpam = true;
        }

        //calculation
        if ($this->request->getSession()->get('calculationAnswer') !== $data['calculationAnswer']) {
            $isSpam = true;
        }

        // form filling duration
        if (self::FORM_FILLING_MINIMAL_TIME > $data['form_filling_duration']) {
            $isSpam = true;
        }

        //throw exception if spam detected
        if ($isSpam) {
            throw new FormValidationException('spam detected !');
        }
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