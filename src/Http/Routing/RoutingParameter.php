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

namespace N86io\Rest\Http\Routing;

/**
 * Class RoutingParameter
 *
 * @author Viktor Firus <v@n86.io>
 */
class RoutingParameter implements RoutingParameterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var boolean
     */
    protected $optional;

    /**
     * @var int
     */
    protected $takeResult;

    /**
     * RoutingParameter constructor.
     * @param string $name
     * @param string $expression
     * @param int $takeResult
     * @param boolean $optional
     */
    public function __construct($name, $expression, $optional, $takeResult = 1)
    {
        $this->name = $name;
        $this->expression = $expression;
        $this->optional = $optional === true;
        $this->takeResult = $takeResult;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return boolean
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @return int
     */
    public function getTakeResult()
    {
        return $this->takeResult;
    }
}
