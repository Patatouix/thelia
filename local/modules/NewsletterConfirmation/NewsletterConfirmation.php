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

namespace NewsletterConfirmation;

use Thelia\Install\Database;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\Base\ModuleConfigQuery;
use Thelia\Module\BaseModule;
use Thelia\Model\MessageQuery;
use Thelia\Model\Message;
use Thelia\Model\LangQuery;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ConfigQuery;

class NewsletterConfirmation extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'newsletterconfirmation';

    protected $translator;

    public function postActivation(ConnectionInterface $con = null)
    {
        ConfigQuery::write('notify_newsletter_subscription', 1);

        $database = new Database($con);
        $database->insertSql(null, array(__DIR__ . '/Config/sql/create.sql'));

        // create new message
        if (null === MessageQuery::create()->findOneByName('newsletter_email_confirmation')) {
            $message = new Message();
            $message
                ->setName('newsletter_email_confirmation')
                ->setHtmlTemplateFileName('newsletter_email_confirmation.html')
                ->setTextTemplateFileName('newsletter_email_confirmation.txt');

            $languages = LangQuery::create()->find();
            foreach ($languages as $language) {
                $locale = $language->getLocale();
                $message->setLocale($locale);
                $message->setSubject(
                    $this->trans('Confirmation of your subscription to {config key="store_name"} newsletter', [], $locale)
                );
                $message->setTitle(
                    $this->trans('Newsletter subscription confirmation message', [], $locale)
                );
            }
            $message->save();
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        if ($deleteModuleData) {
            $database = new Database($con);
            $database->insertSql(null, array(__DIR__ . '/Config/sql/destroy.sql'));
        }
    }

    protected function trans($id, $parameters = [], $locale = null)
    {
        if (null === $this->translator) {
            $this->translator = Translator::getInstance();
        }
        return $this->translator->trans($id, $parameters, self::DOMAIN_NAME, $locale);
    }
}
