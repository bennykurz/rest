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

namespace N86io\Rest;

use N86io\Rest\Http\Routing\RoutingParameter;
use N86io\Rest\Http\Routing\RoutingParameterInterface;

/**
 * Class Dependency
 * @package N86io\Rest
 */
class Dependency
{
    /**
     * @var array
     */
    private static $dependencies = [];

    /**
     * @return array
     */
    public static function get()
    {
        static::setDefaults();
        return static::$dependencies;
    }

    /**
     * @param string $type
     * @param mixed $replacement
     */
    public static function set($type, $replacement)
    {
        static::$dependencies[$type] = $replacement;
    }

    protected static function setDefaults()
    {
        static::setDefault(RoutingParameterInterface::class, \DI\object(RoutingParameter::class));
    }

    /**
     * @param string $type
     * @param mixed $replacement
     */
    protected static function setDefault($type, $replacement)
    {
        if (!array_key_exists($type, static::$dependencies) || empty(static::$dependencies[$type])) {
            static::set($type, $replacement);
        }
    }
}
