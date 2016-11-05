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

namespace N86io\Rest\Persistence\Ordering;

use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;

/**
 * Class Ordering
 *
 * @author Viktor Firus <v@n86.io>
 */
class Ordering implements OrderingInterface
{
    /**
     * @var PropertyInfoInterface
     */
    protected $propertyInfo;

    /**
     * @var int
     */
    protected $direction;

    /**
     * Ordering constructor.
     * @param PropertyInfoInterface $propertyInfo
     * @param int $direction
     */
    public function __construct(PropertyInfoInterface $propertyInfo, $direction)
    {
        $this->checkDirection($direction);
        $this->propertyInfo = $propertyInfo;
        $this->direction = $direction;
    }

    /**
     * @return PropertyInfoInterface
     */
    public function getPropertyInfo()
    {
        return $this->propertyInfo;
    }

    /**
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param int $direction
     */
    protected function checkDirection($direction)
    {
        if ($direction < 1 || $direction > 2) {
            throw new \InvalidArgumentException('Invalid type for ordering.');
        }
    }
}
