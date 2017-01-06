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

namespace N86io\Rest\Http\Routing;

use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
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
     * @var bool
     */
    protected $optional;

    /**
     * @var int
     */
    protected $takeResult;

    /**
     * RoutingParameter constructor.
     *
     * @param string $name
     * @param string $expression
     * @param bool   $optional
     * @param int    $takeResult
     */
    public function __construct(string $name, string $expression, bool $optional, int $takeResult = 1)
    {
        Assert::greaterThanEq($takeResult, 1);
        $this->name = $name;
        $this->expression = $expression;
        $this->optional = $optional;
        $this->takeResult = $takeResult;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * @return int
     */
    public function getTakeResult(): int
    {
        return $this->takeResult;
    }
}
