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
        $this->honeypot = AntiSpam::getConfigValue('honeypot', 1);
        $this->form_fill_duration = AntiSpam::getConfigValue('form_fill_duration', 1);
        $this->question = AntiSpam::getConfigValue('question', 1);
        $this->calculation = AntiSpam::getConfigValue('calculation', 1);
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

    public function onMainBodyBottom(HookRenderEvent $event)
    {
        if ($this->form_fill_duration) {
            $event->add($this->addJS('assets/js/antispam.js'));
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
