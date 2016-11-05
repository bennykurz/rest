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
 * Class Comparison
 *
 * @author Viktor Firus <v@n86.io>
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
     * @param PropertyInfoInterface $leftOperand
     * @param $rightOperand
     * @param int $type
     * @param bool $save
     */
    public function __construct(PropertyInfoInterface $leftOperand, $type, $rightOperand, $save = null)
    {
        $this->checkType($type);
        $this->leftOperand = $leftOperand;
        $this->rightOperand = $rightOperand;
        $this->type = $type;
        $this->save = $save === true;
    }

    /**
     * @return PropertyInfoInterface
     */
    public function getLeftOperand()
    {
        return $this->leftOperand;
    }

    /**
     * @return int
     */
    public function getType()
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
     * @return boolean
     */
    public function isSave()
    {
        return $this->save;
    }

    /**
     * @param $type
     */
    protected function checkType($type)
    {
        if ($type < 1 || $type > 8) {
            throw new \InvalidArgumentException('Invalid type for comparison operator.');
        }
    }
}
