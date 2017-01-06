<?php declare(strict_types = 1);
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

use N86io\Di\Singleton;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
interface AuthorizationInterface extends Singleton
{
    /**
     * @param int $userGroup
     */
    public function addUserGroup(int $userGroup);

    /**
     * @param array $userGroups
     */
    public function addUserGroups(array $userGroups);

    /**
     * @param string $model
     * @param int    $requestMode
     *
     * @return bool
     */
    public function hasApiAccess(string $model, int $requestMode): bool;

    /**
     * @param string $model
     * @param string $propertyName
     *
     * @return bool
     */
    public function hasPropertyReadAuthorization(string $model, string $propertyName): bool;
}
