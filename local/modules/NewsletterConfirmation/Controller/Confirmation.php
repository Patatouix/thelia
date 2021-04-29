<?php

namespace NewsletterConfirmation\Controller;

use NewsletterConfirmation\Model\NewsletterConfirmationQuery;
use NewsletterConfirmation\NewsletterConfirmation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Newsletter;
use Thelia\Model\NewsletterQuery;
use Thelia\Tools\URL;

class Confirmation extends BaseAdminController
{
    public function confirm(Request $request)
    {
        $newsletterConfirmationId = $request->get('id');
        $tokenFromUrl = $request->get('token');

        $newsletterConfirmation = NewsletterConfirmationQuery::create()->findPk($newsletterConfirmationId);
        $tokenFromDb = $newsletterConfirmation->getConfirmationToken();

        if ($tokenFromUrl === $tokenFromDb) {
            $newsletter = $newsletterConfirmation->getNewsletter();
            $newsletter
                ->setUnsubscribed(0)
                ->save();
        }
    }
}