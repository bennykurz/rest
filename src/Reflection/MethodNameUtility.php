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

namespace N86io\Rest\Reflection;

use N86io\Rest\Object\SingletonInterface;

/**
 * Class MethodNameUtility
 *
 * @author Viktor Firus <v@n86.io>
 */
class MethodNameUtility implements SingletonInterface
{
    /**
     * @param string $methodName
     * @return string
     */
    public function createPropertyNameFromMethod($methodName)
    {
        preg_match('/^([a-z]+)([a-zA-Z]*)/', $methodName, $matches);
        return lcfirst($matches[2]);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public function isGetterOrSetter($methodName)
    {
        return $this->isGetter($methodName) || $this->isSetter($methodName);
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public function isGetter($methodName)
    {
        return preg_match('/^(is|get).*/', $methodName) === 1;
    }

    /**
     * @param string $methodName
     * @return bool
     */
    public function isSetter($methodName)
    {
        return preg_match('/^(set).*/', $methodName) === 1;
    }
}
