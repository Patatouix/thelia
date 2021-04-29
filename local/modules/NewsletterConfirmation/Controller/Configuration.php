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

namespace NewsletterConfirmation\Controller;

use NewsletterConfirmation\NewsletterConfirmation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

class Configuration extends BaseAdminController
{
    public function editConfiguration()
    {
        if (null !== $response = $this->checkAuth(
            AdminResources::MODULE,
            [NewsletterConfirmation::DOMAIN_NAME],
            AccessManager::UPDATE
        )) {
            return $response;
        }

        $form = $this->createForm('newsletterconfirmation.configuration');
        $error_message = null;

        try {
            $validateForm = $this->validateForm($form);
            $data = $validateForm->getData();

            NewsletterConfirmation::setConfigValue(
                'newsletter_email_confirmation',
                is_bool($data["enabled"]) ? (int) ($data["enabled"]) : $data["enabled"]
            );

            return $this->redirectToConfigurationPage();

        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $error_message) {
            $this->setupFormErrorContext(
                'configuration',
                $error_message,
                $form
            );
            $response = $this->render("module-configure", ['module_code' => 'NewsletterConfirmation']);
        }
        return $response;
    }

    /**
     * Redirect to the configuration page
     */
    protected function redirectToConfigurationPage()
    {
        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/NewsletterConfirmation'));
    }
}