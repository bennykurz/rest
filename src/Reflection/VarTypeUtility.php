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

namespace N86io\Rest\Reflection;

use N86io\Di\Singleton;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class VarTypeUtility implements Singleton
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function isDefaultType(string $type): bool
    {
        return $this->isBoolean($type) || $this->isInteger($type) || $this->isFloatingPointNumber($type) ||
            $this->isDateTime($type) || $type === 'string' || $type === 'array';
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isDateTime(string $type): bool
    {
        return $type === '\DateTime' || $type === 'DateTime';
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isBoolean(string $type): bool
    {
        return $type === 'bool' || $type === 'boolean';
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isInteger(string $type): bool
    {
        return $type === 'int' || $type === 'integer';
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isFloatingPointNumber(string $type): bool
    {
        return $type === 'float' || $type === 'double';
    }
}
