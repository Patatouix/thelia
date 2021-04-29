<?php

namespace NewsletterConfirmation\Form;

use NewsletterConfirmation\NewsletterConfirmation;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class Configuration extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "enabled",
                "checkbox",
                array(
                    "label" => "Enabled",
                    "label_attr" => [
                        "for" => "enabled",
                        "help" => Translator::getInstance()->trans(
                            'Check if you want to activate newsletter email confirmation',
                            [],
                            NewsletterConfirmation::DOMAIN_NAME
                        )
                    ],
                    "required" => false,
                    "value" => NewsletterConfirmation::getConfigValue('newsletter_email_confirmation', 1),
                )
            );
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "newsletter_confirmation_enable";
    }
}
