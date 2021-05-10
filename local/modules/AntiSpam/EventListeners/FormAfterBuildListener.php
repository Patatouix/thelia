<?php

namespace AntiSpam\EventListeners;

use AntiSpam\AntiSpam;
use AntiSpam\Model\QuizzTrait;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\HttpFoundation\Request;

/**
 * [Description FormAfterBuildListener]
 */
class FormAfterBuildListener implements EventSubscriberInterface
{
    use QuizzTrait;

    const FORM = 'thelia_contact';

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param AreaDeleteEvent $event
     */
    public function addAntiSpamFields(TheliaFormEvent $event)
    {
        $formBuilder = $event->getForm()->getFormBuilder();

        $config = json_decode(AntiSpam::getConfigValue('antispam_config'), true);

        // form filling duration field
        if ($config['form_fill_duration']) {
            $this->addFormFillingDurationField($formBuilder);
        }

        // honeypot field
        if ($config['honeypot']) {
            $this->addHoneypotField($formBuilder);
        }

        // question
        if ($config['question']) {
            $this->addQuestionField($formBuilder);
        }

        // calculation
        if ($config['calculation']) {
            $this->addCalculationField($formBuilder);
        }
    }

    protected function addFormFillingDurationField(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("form_load_time", "hidden", [
            'data' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    protected function addHoneypotField(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("website", "text", [
            "label" => Translator::getInstance()->trans("Website", [], 'antispam'),
            "label_attr" => [
                "for" => "website"
            ],
            "required" => false
        ]);
    }

    protected function addQuestionField(FormBuilderInterface $formBuilder)
    {
        $session = $this->request->getSession();

        if ($this->request->isMethod('get')) {
            $question = $this->getRandomQuestion();
            $session->set('questionLabel', $question['questionLabel']);
            $session->set('questionAnswer', $question['answerLabel']);
        }

        $formBuilder->add("questionAnswer", "text", [
            "constraints" => [
                new NotBlank(),
            ],
            "label" => isset($question) ? $question['questionLabel'] : $session->get('questionLabel'),
            "label_attr" => array(
                "for" => "questionAnswer",
            ),
            "required" => true
        ]);
    }

    protected function addCalculationField(FormBuilderInterface $formBuilder)
    {
        $session = $this->request->getSession();

        if ($this->request->isMethod('get')) {
            $calculation = $this->getRandomCalculation($session->getLang()->getCode());
            $session->set('calculationLabel', $calculation['calculationLabel']);
            $session->set('calculationAnswer', $calculation['calculationAnswer']);
        }

        $formBuilder->add("calculationAnswer", "text", [
            "constraints" => [
                new NotBlank(),
            ],
            "label" => isset($calculation) ? $calculation['calculationLabel'] : $session->get('calculationLabel'),
            "label_attr" => array(
                "for" => "calculationAnswer",
            ),
            "required" => true
        ]);
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