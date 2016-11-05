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

namespace N86io\Rest\Persistence\Constraint;

use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;

/**
 * Interface ComparisonInterface
 *
 * @author Viktor Firus <v@n86.io>
 */
interface ComparisonInterface extends ConstraintInterface
{
    const LESS_THAN = 1;
    const LESS_THAN_OR_EQUAL_TO = 2;
    const GREATER_THAN = 3;
    const GREATER_THAN_OR_EQUAL_TO = 4;
    const EQUAL_TO = 5;
    const NOT_EQUAL_TO = 6;
    const CONTAINS = 7;

    const INTERNAL_REGULAR_EXPRESSION = 8;

    /**
     * @return PropertyInfoInterface
     */
    public function getLeftOperand();

    /**
     * @return mixed
     */
    public function getRightOperand();

    /**
     * @return int
     */
    public function getType();

    /**
     * @return boolean
     */
    public function isSave();
}
