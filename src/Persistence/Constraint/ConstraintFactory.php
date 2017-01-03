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

use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;

/**
 * Class ConstraintFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class ConstraintFactory implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param string                $operator
     * @param                       $rightOperand
     * @param boolean               $save
     *
     * @return ConstraintInterface
     */
    public function createComparisonFromStringDetection(
        PropertyInfoInterface $leftOperand,
        $operator,
        $rightOperand,
        $save = null
    ) {
        switch ($operator) {
            case 'lt':
                return $this->lessThan($leftOperand, $rightOperand, $save);
            case 'lte':
                return $this->lessThanOrEqualTo($leftOperand, $rightOperand, $save);
            case 'gt':
                return $this->greaterThan($leftOperand, $rightOperand, $save);
            case 'gte':
                return $this->greaterThanOrEqualTo($leftOperand, $rightOperand, $save);
            case 'ne':
                return $this->notEqualTo($leftOperand, $rightOperand, $save);
            case 'c':
                return $this->contains($leftOperand, $rightOperand, $save);
        }

        // Default or e
        return $this->equalTo($leftOperand, $rightOperand, $save);
    }

    /**
     * @param array $constraints
     *
     * @return LogicalInterface
     */
    public function logicalAnd(array $constraints)
    {
        return $this->container->get(
            Logical::class,
            $constraints,
            LogicalInterface::OPERATOR_AND
        );
    }

    /**
     * @param array $constraints
     *
     * @return LogicalInterface
     */
    public function logicalOr(array $constraints)
    {
        return $this->container->get(
            Logical::class,
            $constraints,
            LogicalInterface::OPERATOR_OR
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function lessThan(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::LESS_THAN,
            $rightOperand,
            $save
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function lessThanOrEqualTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::LESS_THAN_OR_EQUAL_TO,
            $rightOperand,
            $save
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function greaterThan(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::GREATER_THAN,
            $rightOperand,
            $save
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function greaterThanOrEqualTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::GREATER_THAN_OR_EQUAL_TO,
            $rightOperand,
            $save
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function equalTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::EQUAL_TO,
            $rightOperand,
            $save
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function notEqualTo(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::NOT_EQUAL_TO,
            $rightOperand,
            $save
        );
    }

    /**
     * @param PropertyInfoInterface $leftOperand
     * @param                       $rightOperand
     * @param bool                  $save
     *
     * @return ComparisonInterface
     */
    public function contains(PropertyInfoInterface $leftOperand, $rightOperand, $save = null)
    {
        return $this->container->get(
            Comparison::class,
            $leftOperand,
            ComparisonInterface::CONTAINS,
            $rightOperand,
            $save
        );
    }
}
