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
        $alert = 'danger';

        if (null !== $newsletterConfirmation = NewsletterConfirmationQuery::create()->findPk($request->get('id', null))) {
            $tokenFromDb = $newsletterConfirmation->getConfirmationToken();
            $newsletter = $newsletterConfirmation->getNewsletter();

            //first check if newsletter is unsubscribed
            if ($newsletter->getUnsubscribed()) {
                //compare tokens
                if ($request->get('token', null) === $tokenFromDb) {
                    $event = new NewsletterEvent(
                        $newsletter->getEmail(),
                        $newsletter->getLocale()
                    );
                    $event->setNewsletter($newsletter);

                    $this->dispatch(TheliaEvents::NEWSLETTER_CONFIRM_SUBSCRIPTION, $event);

                    $alert = 'success';
                }
            } else {
                $alert = 'info';
            }
        }

        return $this->render('newsletter', [
            'alert' => $alert,
            'email' => ($alert !== 'danger' ? $newsletter->getEmail() : null)
        ]);
    }
}