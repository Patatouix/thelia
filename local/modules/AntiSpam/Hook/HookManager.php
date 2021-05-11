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
    protected $config;

    public function __construct()
    {
        $this->config = json_decode(AntiSpam::getConfigValue('antispam_config'), true);
    }

    public function onContactFormTop(HookRenderEvent $event)
    {
        if ($this->config['honeypot'] || $this->config['form_fill_duration'] || $this->config['question']) {
            $event->add($this->render("antispam_alert.html", $this->getAllArguments($event)));
        }
    }

    public function onContactFormBottom(HookRenderEvent $event)
    {
        if ($this->config['honeypot']) {
            $event->add($this->render("antispam_honeypot.html", $this->getAllArguments($event)));
        }

        if ($this->config['question']) {
            $event->add($this->render("antispam_question.html", $this->getAllArguments($event)));
        }
    }

    public function onContactStylesheet(HookRenderEvent $event)
    {
        if ($this->config['honeypot']) {
            $event->add($this->addCSS('assets/css/antispam.css'));
        }
    }

    public function onContactJSInitialization(HookRenderEvent $event)
    {
        if ($this->config['question']) {
            $event->add($this->addJS("assets/js/question_refresh.js"));
        }
    }

    protected function getAllArguments(HookRenderEvent $event)
    {
        return $event->getTemplateVars() + $event->getArguments();
    }
}
