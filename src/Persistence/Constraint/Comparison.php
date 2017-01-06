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

namespace N86io\Rest\Persistence\Constraint;

use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Comparison implements ComparisonInterface
{
    /**
     * @var PropertyInfoInterface
     */
    protected $leftOperand;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $rightOperand;

    /**
     * @var bool
     */
    protected $save;

    /**
     * ComparisonOperator constructor.
     *
     * @param PropertyInfoInterface $leftOperand
     * @param int                   $type
     * @param                       $rightOperand
     * @param bool                  $save
     */
    public function __construct(PropertyInfoInterface $leftOperand, int $type, $rightOperand, bool $save)
    {
        $this->checkType($type);
        $this->leftOperand = $leftOperand;
        $this->rightOperand = $rightOperand;
        $this->type = $type;
        $this->save = $save;
    }

    /**
     * @return PropertyInfoInterface
     */
    public function getLeftOperand(): PropertyInfoInterface
    {
        return $this->leftOperand;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getRightOperand()
    {
        return $this->rightOperand;
    }

    /**
     * @return bool
     */
    public function isSave(): bool
    {
        return $this->save;
    }

    /**
     * @param int $type
     *
     * @throws \InvalidArgumentException
     */
    protected function checkType(int $type)
    {
        if ($type < 1 || $type > 8) {
            throw new \InvalidArgumentException('Invalid type for comparison operator.');
        }
    }
}
