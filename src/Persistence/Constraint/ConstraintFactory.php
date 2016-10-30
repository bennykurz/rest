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
 * Class ConstraintFactory
 * @package N86io\Rest\Persistence\Constraint
 */
class ConstraintFactory
{
    /**
     * @param array $constraints
     * @return ConstraintInterface
     */
    public function logicalAnd(array $constraints)
    {
        return new Logical($constraints, LogicalInterface::OPERATOR_AND);
    }

    /**
     * @param array $constraints
     * @return ConstraintInterface
     */
    public function logicalOr(array $constraints)
    {
        return new Logical($constraints, LogicalInterface::OPERATOR_OR);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function lessThan(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::LESS_THAN, $rightOperand, $save);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function lessThanOrEqualTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::LESS_THAN_OR_EQUAL_TO, $rightOperand, $save);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function greaterThan(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::GREATER_THAN, $rightOperand, $save);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function greaterThanOrEqualTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::GREATER_THAN_OR_EQUAL_TO, $rightOperand, $save);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function equalTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::EQUAL_TO, $rightOperand, $save);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function notEqualTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::NOT_EQUAL_TO, $rightOperand, $save);
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param mixed $rightOperand
     * @param bool $save
     * @return ConstraintInterface
     */
    public function contains(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return new Comparison($leftOperand, ComparisonInterface::CONTAINS, $rightOperand, $save);
    }
}
