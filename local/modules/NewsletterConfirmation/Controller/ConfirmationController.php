<?php

namespace NewsletterConfirmation\Controller;

use NewsletterConfirmation\Model\NewsletterConfirmationQuery;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Newsletter\NewsletterEvent;

class ConfirmationController extends BaseFrontController
{
    public function confirm(Request $request)
    {
        $newsletterConfirmationId = $request->get('id');
        $tokenFromUrl = $request->get('token');

        $success = false;

        if ($newsletterConfirmationId && $tokenFromUrl) {
            if (null !== $newsletterConfirmation = NewsletterConfirmationQuery::create()->findPk($newsletterConfirmationId)) {
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

                    $success = true;
                }
            }
        }

        return $this->render('newsletter', [
            'success' => $success,
            'email' => ($success ? $newsletter->getEmail() : null)
        ]);
    }
}