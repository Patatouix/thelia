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
use Thelia\Model\Base\ModuleConfigQuery;
use Thelia\Module\BaseModule;

class AntiSpam extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'antispam';

    public function postActivation(ConnectionInterface $con = null)
    {
        $antispamConfig = [
            'honeypot' => 1,
            'form_fill_duration' => 1,
            'form_fill_duration_limit' => 3,
            'question' => 1,
            'calculation' => 1
        ];

        self::setConfigValue('antispam_config', json_encode($antispamConfig));
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        if ($deleteModuleData) {
            $configQuery = ModuleConfigQuery::create();
            $configQuery->deleteConfigValue(self::getModuleId(), 'antispam_config');
        }
    }
}
