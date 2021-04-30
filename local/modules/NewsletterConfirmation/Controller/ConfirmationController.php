<?php

namespace NewsletterConfirmation\Controller;

use NewsletterConfirmation\Model\NewsletterConfirmationQuery;
use NewsletterConfirmation\NewsletterConfirmation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Controller\BaseController;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\Newsletter;
use Thelia\Model\NewsletterQuery;
use Thelia\Tools\URL;
use Thelia\Model\ConfigQuery;
use Thelia\Core\Event\Newsletter\NewsletterEvent;

class ConfirmationController extends BaseFrontController
{
    public function confirm(Request $request)
    {
        $newsletterConfirmationId = $request->get('id');
        $tokenFromUrl = $request->get('token');

        if ($newsletterConfirmationId && $tokenFromUrl) {
            $newsletterConfirmation = NewsletterConfirmationQuery::create()->findPk($newsletterConfirmationId);
            $tokenFromDb = $newsletterConfirmation->getConfirmationToken();

            //compare tokens
            if ($tokenFromUrl === $tokenFromDb) {

                $newsletter = $newsletterConfirmation->getNewsletter();
                $event = new NewsletterEvent(
                    $newsletter->getEmail(),
                    $newsletter->getLocale()
                );
                $event->setNewsletter($newsletter);

                $this->dispatch(TheliaEvents::NEWSLETTER_CONFIRM_SUBSCRIPTION, $event);
            }

            return $this->render('newsletter-confirmation', [
                'email' => $newsletter->getEmail()
            ]);
        }
        else
        {
            return $this->render('newsletter-confirmation-error');
        }
    }
}