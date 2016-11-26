<?php
/**
 * This file is part of N86io/Rest.
 *
 * N86io/Rest is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * N86io/Rest is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with N86io/Rest or see <http://www.gnu.org/licenses/>.
 */

namespace N86io\Rest\Authentication;

use Lcobucci\JWT\Parser;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class UserAuthentication
 *
 * @author Viktor Firus <v@n86.io>
 */
class UserAuthentication implements UserAuthenticationInterface
{
    /**
     * @var AuthenticationConfiguration
     */
    protected $authConf = [];

    /**
     * @var array
     */
    protected $userGroups = [-1];

    public function load()
    {
        $headerAuthorization = getallheaders()['Authorization'];
        if (substr($headerAuthorization, 0, 6) !== 'Bearer') {
            return;
        }
        $token = substr($headerAuthorization, 7);
        $jwtParser = new Parser;
        $token = $jwtParser->parse($token);
        DebuggerUtility::var_dump($token);


        die();
    }

    /**
     * @param string $model
     * @param int $requestMode
     * @return boolean
     */
    public function hasApiAccess($model, $requestMode)
    {
        $allowedRead = false;
        $allowedWrite = false;
        foreach ($this->authConf->getAccessConf()[$model] as $groupId => $groupItem) {
            if (array_search($groupId, $this->userGroups) === false) {
                continue;
            }
            if (empty($groupItem['access'])) {
                continue;
            }
            if (array_search('read', $groupItem['access']) !== false) {
                $allowedRead = true;
            }
            if (array_search('write', $groupItem['access']) !== false) {
                $allowedWrite = true;
            }
        }
        return AccessUtility::canAccess($requestMode, $allowedRead, $allowedWrite);
    }

    /**
     * @param AuthenticationConfiguration $authConf
     */
    public function setAuthenticationConfiguration(AuthenticationConfiguration $authConf)
    {
        $this->authConf = $authConf;
    }
}
