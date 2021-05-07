<?php

namespace AntiSpam\EventListeners;

use AntiSpam\AntiSpam;
use NumberFormatter;
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
    const FORM = 'thelia_contact';
    const QUESTIONS = [
        "What is the color of Henri IV's white horse ?" => "white",
        "What is between yesterday and tomorrow ?" => "today",
        "How many legs does have a dog ?" => "four",
        "Which number is the largest, 7 or 9?" => "nine",
        "Which number is the smallest, 6 or 2 ?" => "two",
        "Which number is the largest, 3 or 7?" => "seven",
        "Which number is the largest, 6 or 5?" => "six",
        "Which number is the smallest, 8 or 9?" => "eight",
        "Which number is the largest, 8 or 2 ?" => "eight"
    ];
    const OPERATORS = ['+', '-', '*'];

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

        // form filling duration field
        if (AntiSpam::getConfigValue('form_filling_duration', 1)) {
            $this->addFormFillingDurationField($formBuilder);
        }

        // honeypot field
        if (AntiSpam::getConfigValue('honeypot', 1)) {
            $this->addHoneypotField($formBuilder);
        }

        // question
        if (AntiSpam::getConfigValue('question', 1)) {
            $this->addQuestionField($formBuilder);
        }

        // calculation
        if (AntiSpam::getConfigValue('calculation', 1)) {
            $this->addCalculationField($formBuilder);
        }
    }

    protected function addFormFillingDurationField(FormBuilderInterface $formBuilder)
    {
        $formBuilder->add("form_filling_duration", "hidden", [
            'attr' => [
                'class' => 'form_filling_duration'
            ]
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
            $questionLabel = array_rand(self::QUESTIONS, 1);
            $session->set('questionLabel', Translator::getInstance()->trans($questionLabel, [], 'antispam'));
            $session->set('questionAnswer', Translator::getInstance()->trans(self::QUESTIONS[$questionLabel], [], 'antispam'));
        } elseif ($this->request->isMethod('post')) {
            $questionLabel = $session->get('questionLabel');
        }

        $formBuilder->add("questionAnswer", "text", [
            "constraints" => [
                new NotBlank(),
            ],
            "label" => Translator::getInstance()->trans($questionLabel, [], 'antispam'),
            "label_attr" => array(
                "for" => "questionAnswer",
            ),
            "required" => true
        ]);
    }

    protected function addCalculationField(FormBuilderInterface $formBuilder)
    {
        $session = $this->request->getSession();

        $formatter = new NumberFormatter($session->getLang()->getCode(), NumberFormatter::SPELLOUT);

        if ($this->request->isMethod('get')) {
            $operator = self::OPERATORS[array_rand(self::OPERATORS, 1)];
            $numbers = [
                rand(0, 9),
                rand(0, 9)
            ];
            switch ($operator) {
                case "+":
                    $calculationAnswer = $numbers[0] + $numbers[1];
                    break;
                case "-":
                    $calculationAnswer = $numbers[0] - $numbers[1];
                    break;
                case "*":
                    $calculationAnswer = $numbers[0] * $numbers[1];
                    break;
            }
            $rand = rand(0, 1);
            $calculationLabel = Translator::getInstance()->trans("How much are : ", [], 'antispam')
                . ($rand ? $numbers[0] : $formatter->format($numbers[0]))
                . " "
                . $operator
                . " "
                . ($rand ? $formatter->format($numbers[1]) : $numbers[1])
                . " ?";
            $session->set('calculationLabel', $calculationLabel);
            $session->set('calculationAnswer', $formatter->format($calculationAnswer));
        } elseif ($this->request->isMethod('post')) {
            $calculationLabel = $session->get('calculationLabel');
        }

        $formBuilder->add("calculationAnswer", "text", [
            "constraints" => [
                new NotBlank(),
            ],
            "label" => $calculationLabel,
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