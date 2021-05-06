<?php

namespace AntiSpam\EventListeners;

use NumberFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\Translation\Translator;
use Thelia\Mailer\MailerFactory;
use Thelia\Tools\TokenProvider;
use Thelia\Model\ConfigQuery;
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
        "Quel chiffre est le plus grand, 7 ou 9?" => "nine",
        "Quel chiffre est le plus petit, 6 ou 2 ?" => "two",
        "Quel chiffre est le plus grand, 3 ou 7?" => "seven",
        "Quel chiffre est le plus grand, 6 ou 5?" => "six",
        "Quel chiffre est le plus petit, 8 ou 9?" => "eight",
        "Quel chiffre est le plus grand, 8 ou 2 ?" => "eight"
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
        $session = $this->request->getSession();

        $formBuilder = $event->getForm()->getFormBuilder();

        // form filling duration field
        $formBuilder->add("form_filling_duration", "hidden", [
            'attr' => [
                'class' => 'form_filling_duration'
            ]
        ]);

        // honeypot field
        $formBuilder->add("website", "text", [
            "label" => Translator::getInstance()->trans("Website"),
            "label_attr" => [
                "for" => "website"
            ],
            "required" => false
        ]);

        // question
        if ($this->request->isMethod('get')) {
            $questionLabel = array_rand(self::QUESTIONS, 1);
            $session->set('questionLabel', $questionLabel);
            $session->set('questionAnswer', self::QUESTIONS[$questionLabel]);
        } elseif ($this->request->isMethod('post')) {
            $questionLabel = $session->get('questionLabel');
        }

        $formBuilder->add("questionAnswer", "text", [
            "constraints" => [
                new NotBlank(),
            ],
            "label" => Translator::getInstance()->trans($questionLabel),
            "label_attr" => array(
                "for" => "questionAnswer",
            ),
            "required" => true
        ]);

        // calcul
        $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);

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
            $calculationLabel = "Combien font : "
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
            "label" => Translator::getInstance()->trans($calculationLabel),
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