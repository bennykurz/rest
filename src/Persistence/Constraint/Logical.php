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

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Logical implements LogicalInterface
{
    /**
     * @var array
     */
    protected $constraints = [];

    /**
     * @var int
     */
    protected $type;

    /**
     * @param array $constraints
     * @param int   $type
     */
    public function __construct(array $constraints, int $type)
    {
        $this->checkType($type);
        $this->checkConstraints($constraints);
        $this->constraints = $constraints;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param array $constraints
     *
     * @throws \InvalidArgumentException
     */
    protected function checkConstraints(array $constraints)
    {
        if (empty($constraints)) {
            throw new \InvalidArgumentException('It\'s impossible to create logical operator without constraints.');
        }
        foreach ($constraints as $constraint) {
            if (!$constraint instanceof ConstraintInterface) {
                throw new \InvalidArgumentException('Invalid constraint given for logical operator.');
            }
        }
    }

    /**
     * @param int $type
     *
     * @throws \InvalidArgumentException
     */
    protected function checkType(int $type)
    {
        if ($type < 1 || $type > 2) {
            throw new \InvalidArgumentException('Invalid type for logical operator.');
        }
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->constraints);
    }

    public function next()
    {
        next($this->constraints);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->constraints);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return current($this->constraints) !== false;
    }

    public function rewind()
    {
        reset($this->constraints);
    }
}
