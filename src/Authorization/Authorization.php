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

namespace N86io\Rest\Authorization;

use Webmozart\Assert\Assert;

/**
 * Class Authorization
 *
 * @author Viktor Firus <v@n86.io>
 */
class Authorization implements AuthorizationInterface
{
    /**
     * @inject
     * @var Configuration
     */
    protected $authConf;

    /**
     * @var array
     */
    protected $userGroups = [-1];

    /**
     * @param int $userGroup
     */
    public function addUserGroup($userGroup)
    {
        Assert::integer($userGroup);
        $this->userGroups[] = $userGroup;
    }

    /**
     * @param array $userGroups
     */
    public function addUserGroups(array $userGroups)
    {
        $this->userGroups = array_merge($this->userGroups, $userGroups);
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
            if ($allowedRead && $allowedWrite) {
                break;
            }
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
        return Utility::canAccess($requestMode, $allowedRead, $allowedWrite);
    }

    /**
     * @param string $model
     * @param string $propertyName
     * @return bool
     */
    public function hasPropertyReadAuthorization($model, $propertyName)
    {
        return $this->hasPropertyAuthorization($model, $propertyName)[0];
    }

    /**
     * @param string $model
     * @param string $propertyName
     * @return bool[]
     */
    protected function hasPropertyAuthorization($model, $propertyName)
    {
        $allowedRead = false;
        $allowedWrite = false;

        foreach ($this->authConf->getAccessConf()[$model] as $groupId => $groupItem) {
            if ($allowedRead && $allowedWrite) {
                break;
            }
            if (array_search($groupId, $this->userGroups) === false) {
                continue;
            }
            if (empty($groupItem['properties']) || empty($groupItem['properties'][$propertyName])) {
                continue;
            }
            if (array_search('read', $groupItem['properties'][$propertyName]) !== false) {
                $allowedRead = true;
            }
            if (array_search('write', $groupItem['properties'][$propertyName]) !== false) {
                $allowedWrite = true;
            }
        }

        return [$allowedRead, $allowedWrite];
    }
}
