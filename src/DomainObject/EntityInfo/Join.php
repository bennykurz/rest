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

namespace N86io\Rest\DomainObject\EntityInfo;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Join implements JoinInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $condition;

    /**
     * @param string $alias
     * @param string $table
     * @param string $condition
     */
    public function __construct(string $alias, string $table, string $condition)
    {
        $this->alias = $alias;
        $this->table = $table;
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'inner';
    }
}
