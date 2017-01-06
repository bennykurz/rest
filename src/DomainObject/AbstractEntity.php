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

namespace N86io\Rest\DomainObject;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var int
     */
    protected $deleted;

    /**
     * @var int
     */
    protected $disabled;

    /**
     * @var int
     */
    protected $startTime;

    /**
     * @var int
     */
    protected $endTime;

    /**
     * @param string $propertyName
     * @param mixed $propertyValue
     * @internal
     */
    final public function setProperty(string $propertyName, $propertyValue)
    {
        $this->{$propertyName} = $propertyValue;
    }

    /**
     * @param string $propertyName
     * @return mixed
     * @internal
     */
    final public function getProperty(string $propertyName)
    {
        return $this->{$propertyName};
    }

    /**
     * @return array
     * @internal
     */
    final public function getProperties(): array
    {
        return get_object_vars($this);
    }
}
