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

namespace N86io\Rest\DomainObject\PropertyInfo;

/**
 * Class JoinAliasStorage
 * @package N86io\Rest\DomainObject\PropertyInfo
 * @Injectable(scope="prototype")
 */
class JoinAliasStorage
{
    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @param $table
     * @return mixed|string
     */
    public function get($table)
    {
        if (!array_key_exists($table, $this->aliases)) {
            $this->counter++;
            $joinAlias = 'j' . $this->counter;
            $this->aliases[$table] = $joinAlias;
        }
        return $this->aliases[$table];
    }

    public function reset()
    {
        $this->counter = 0;
        $this->aliases = [];
    }
}
