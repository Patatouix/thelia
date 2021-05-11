<?php

namespace AntiSpam\EventListeners;

use AntiSpam\AntiSpam;
use AntiSpam\Model\QuestionGeneratorTrait;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    use QuestionGeneratorTrait;

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
    }

    protected function addFormFillingDurationField(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("form_load_time", HiddenType::class, [
            'data' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    protected function addHoneypotField(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("bear", TextType::class, [
            "label" => Translator::getInstance()->trans("Bear", [], 'antispam'),
            "label_attr" => [
                "for" => "bear"
            ],
            "required" => false
        ]);
    }

    protected function addQuestionField(FormBuilderInterface $formBuilder)
    {
        $session = $this->request->getSession();

        if ($this->request->isMethod('get')) {
            $question = $this->getRandomQuestion($session->getLang()->getCode());
            $session->set('questionLabel', $question['questionLabel']);
            $session->set('questionAnswer', $question['answerLabel']);
        }

        $formBuilder->add("questionAnswer", TextType::class, [
            "constraints" => [
                new NotBlank(),
            ],
            "label" => isset($question) ? $question['questionLabel'] : $session->get('questionLabel'),
            "label_attr" => array(
                "for" => "questionAnswer",
                "help" => Translator::getInstance()->trans("Your answer must be written in words", [], 'antispam')
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