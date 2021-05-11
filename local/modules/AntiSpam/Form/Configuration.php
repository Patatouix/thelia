<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace AntiSpam\Form;

use AntiSpam\AntiSpam;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * Class Configuration
 * @package AntiSpam\Form
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class Configuration extends BaseForm
{

    protected function buildForm()
    {
        $antispamConfig = json_decode(AntiSpam::getConfigValue('antispam_config'), true);

        $this->formBuilder
            ->add(
                "honeypot",
                CheckboxType::class,
                array(
                    "label" => Translator::getInstance()->trans("Honeypot : ", [], 'antispam'),
                    "label_attr" => [
                        "for" => "honeypot",
                        "help" => Translator::getInstance()->trans('Check if you want to activate honeypot', [], 'antispam')
                    ],
                    "required" => false,
                    "value" => $antispamConfig['honeypot']
                )
            )
            ->add(
                "form_fill_duration",
                CheckboxType::class,
                array(
                    "label" => Translator::getInstance()->trans("Form fill duration : ", [], 'antispam'),
                    "label_attr" => [
                        "for" => "form_fill_duration",
                        "help" => Translator::getInstance()->trans('Check if you want to activate form fill duration check', [], 'antispam')
                    ],
                    "required" => false,
                    "value" => $antispamConfig['form_fill_duration']
                )
            )
            ->add(
                "form_fill_duration_limit",
                NumberType::class,
                array(
                    "label"      => Translator::getInstance()->trans("Form fill duration limit (in seconds) : ", [], 'antispam'),
                    "label_attr" => array(
                        "for" => "form_fill_duration_limit",
                        "help" => Translator::getInstance()->trans('If the form is submitted faster than this value, it will be considered as spam', [], 'antispam')
                    ),
                    "constraints" => array(
                        new GreaterThan(["value" => 0]),
                    ),
                    "data" => $antispamConfig['form_fill_duration_limit'],
                    "scale" => 0
                )
            )
            ->add(
                "question",
                CheckboxType::class,
                array(
                    "label" => Translator::getInstance()->trans("Question : ", [], 'antispam'),
                    "label_attr" => [
                        "for" => "question",
                        "help" => Translator::getInstance()->trans('Check if you want to activate question', [], 'antispam')
                    ],
                    "required" => false,
                    "value" => $antispamConfig['question']
                )
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "antispam_configuration";
    }
}
