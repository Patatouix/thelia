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

namespace AntiSpam;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Module\BaseModule;

class AntiSpam extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'antispam';

    public function postActivation(ConnectionInterface $con = null)
    {
        self::setConfigValue('honeypot', 1);
        self::setConfigValue('form_fill_duration', 1);
        self::setConfigValue('question', 1);
        self::setConfigValue('calculation', 1);
    }
}
