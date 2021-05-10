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

namespace AntiSpam\Hook;

use AntiSpam\AntiSpam;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class HookManager extends BaseHook
{
    protected $honeypot;
    protected $form_fill_duration;
    protected $question;
    protected $calculation;

    public function __construct()
    {
        $config = json_decode(AntiSpam::getConfigValue('antispam_config'), true);

        $this->honeypot = $config['honeypot'];
        $this->form_fill_duration = $config['form_fill_duration'];
        $this->question = $config['question'];
        $this->calculation = $config['calculation'];
    }

    public function onContactFormTop(HookRenderEvent $event)
    {
        if ($this->honeypot || $this->form_fill_duration || $this->question || $this->calculation) {
            $event->add($this->render("antispam_alert.html", $this->getAllArguments($event)));
        }
    }

    public function onContactFormBottom(HookRenderEvent $event)
    {
        if ($this->honeypot) {
            $event->add($this->render("antispam_honeypot.html", $this->getAllArguments($event)));
        }

        if ($this->question || $this->calculation) {
            $event->add($this->render("antispam_fields.html", $this->getAllArguments($event)));
        }
    }

    public function onMainStylesheet(HookRenderEvent $event)
    {
        if ($this->honeypot) {
            $event->add($this->addCSS('assets/css/antispam.css'));
        }
    }

    protected function getAllArguments(HookRenderEvent $event)
    {
        return $event->getTemplateVars() + $event->getArguments() + [
            'honeypot' => $this->honeypot,
            'form_fill_duration' => $this->form_fill_duration,
            'question' => $this->question,
            'calculation' => $this->calculation
        ];
    }
}
