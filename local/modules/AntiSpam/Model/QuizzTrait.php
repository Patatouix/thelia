<?php

namespace AntiSpam\Model;

use Carousel\Form\CarouselImageForm;
use Carousel\Model\Base\Carousel as BaseCarousel;
use NumberFormatter;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Files\FileModelInterface;
use Thelia\Files\FileModelParentInterface;
use Thelia\Form\BaseForm;

trait QuizzTrait
{
    public function getRandomQuestion()
    {
        $questions = [
            "What is the color of Henri IV's white horse ?" => "white",
            "What is between yesterday and tomorrow ?" => "today",
            "How many legs does have a dog ?" => "four",
            "Which number is the largest, 7 or 9 ?" => "nine",
            "Which number is the smallest, 6 or 2 ?" => "two",
            "Which number is the largest, 3 or 7 ?" => "seven",
            "Which number is the largest, 6 or 5 ?" => "six",
            "Which number is the smallest, 8 or 9 ?" => "eight",
            "Which number is the largest, 8 or 2 ?" => "eight"
        ];

        $questionLabel = array_rand($questions, 1);

        return [
            'questionLabel' => Translator::getInstance()->trans($questionLabel, [], 'antispam'),
            'answerLabel' => Translator::getInstance()->trans($questions[$questionLabel], [], 'antispam')
        ];
    }

    public function getRandomCalculation($langCode)
    {
        $operators = ['+', '-', '*'];

        $formatter = new NumberFormatter($langCode, NumberFormatter::SPELLOUT);

        $operator = $operators[array_rand($operators, 1)];
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

        return [
            'calculationLabel' => $calculationLabel,
            'calculationAnswer' => $formatter->format($calculationAnswer)
        ];
    }
}
