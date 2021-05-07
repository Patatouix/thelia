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
use AntiSpam\Model\Config\Base\AntiSpamConfigValue;
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
        $this->formBuilder
            ->add(
                "honeypot",
                "checkbox",
                array(
                    "label" => "honeypot",
                    "label_attr" => [
                        "for" => "honeypot",
                        "help" => Translator::getInstance()->trans(
                            'Check if you want to activate honeypot',
                            [],
                            AntiSpam::DOMAIN_NAME
                        )
                    ],
                    "required" => false,
                    "value" => AntiSpam::getConfigValue('honeypot', 1),
                )
            )
            ->add(
                "form_fill_duration",
                "checkbox",
                array(
                    "label" => "form_fill_duration",
                    "label_attr" => [
                        "for" => "form_fill_duration",
                        "help" => Translator::getInstance()->trans(
                            'Check if you want to activate form_fill_duration',
                            [],
                            AntiSpam::DOMAIN_NAME
                        )
                    ],
                    "required" => false,
                    "value" => AntiSpam::getConfigValue('form_fill_duration', 1),
                )
            )
            ->add(
                "question",
                "checkbox",
                array(
                    "label" => "question",
                    "label_attr" => [
                        "for" => "question",
                        "help" => Translator::getInstance()->trans(
                            'Check if you want to activate question',
                            [],
                            AntiSpam::DOMAIN_NAME
                        )
                    ],
                    "required" => false,
                    "value" => AntiSpam::getConfigValue('question', 1),
                )
            )
            ->add(
                "calculation",
                "checkbox",
                array(
                    "label" => "calculation",
                    "label_attr" => [
                        "for" => "calculation",
                        "help" => Translator::getInstance()->trans(
                            'Check if you want to activate calculation',
                            [],
                            AntiSpam::DOMAIN_NAME
                        )
                    ],
                    "required" => false,
                    "value" => AntiSpam::getConfigValue('calculation', 1),
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
