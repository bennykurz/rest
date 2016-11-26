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

use N86io\Rest\Object\Singleton;

/**
 * Interface UserAuthenticationInterface
 *
 * @author Viktor Firus <v@n86.io>
 */
interface UserAuthenticationInterface extends Singleton
{
    public function load();

    /**
     * @param string $model
     * @param int $requestMode
     * @return boolean
     */
    public function hasApiAccess($model, $requestMode);

    /**
     * @param AuthenticationConfiguration $authConf
     */
    public function setAuthenticationConfiguration(AuthenticationConfiguration $authConf);
}
