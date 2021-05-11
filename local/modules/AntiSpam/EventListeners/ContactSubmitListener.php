<?php

namespace AntiSpam\EventListeners;

use AntiSpam\AntiSpam;
use DateTime;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Contact\ContactEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;

/**
 * [Description FormAfterBuildListener]
 */
class ContactSubmitListener implements EventSubscriberInterface
{
    //the minimal amount of time necessary for human to fill contact form
    //const FORM_FILLING_MINIMAL_TIME = 3000;

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

        $config = json_decode(AntiSpam::getConfigValue('antispam_config'), true);

        //honeypot
        if ($config['honeypot'] && null !== $data['bear']) {
            $isSpam = true;
        }

        //question
        if ($config['question'] && $this->cleanString($this->request->getSession()->get('questionAnswer')) !== $this->cleanString($data['questionAnswer'])) {
            $isSpam = true;
        }

        // form filling duration
        if ($config['form_fill_duration']) {
            try {
                $formFillDuration = (int) date_diff(
                    new DateTime($data['form_load_time']),
                    new DateTime()
                )->format('%s');
            } catch (Exception $e) {
                $isSpam = true;
            }

            if (!$isSpam && $config['form_fill_duration_limit'] > $formFillDuration) {
                $isSpam = true;
            }
        }

        //throw exception if spam detected
        if ($isSpam) {
            throw new FormValidationException(Translator::getInstance()->trans("An error occured during the Antispam verification. Please try again", [], 'antispam'));
        }
    }

    protected function cleanString($string)
    {
        return strtr(
            utf8_decode(
                mb_strtolower($string)
            ),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
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