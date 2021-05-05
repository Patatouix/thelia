<?php

namespace AntiSpam\Controller;

use Symfony\Component\HttpFoundation\Request;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\TheliaEvents;
//use Thelia\Core\Event\Contact\ContactEvent;
use Thelia\Form\Definition\FrontForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;

class ContactController extends BaseFrontController
{
    public function fakeValidation()
    {
        return $this->render('fake_validation');
    }
}