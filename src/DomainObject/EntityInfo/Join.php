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

namespace N86io\Rest\DomainObject\EntityInfo;

use Webmozart\Assert\Assert;

/**
 * Class Join
 *
 * @author Viktor Firus <v@n86.io>
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
     * Join constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        if (!empty($attributes['alias'])) {
            $this->alias = $attributes['alias'];
        }
        Assert::string($attributes['table']);
        $this->table = $attributes['table'];
        if (!empty($attributes['condition'])) {
            $this->condition = $attributes['condition'];
            Assert::string($attributes['condition']);
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'inner';
    }
}
