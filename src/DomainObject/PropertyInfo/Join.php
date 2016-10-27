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
 * Class Join
 * @package N86io\Rest\DomainObject\PropertyInfo
 */
class Join extends DynamicSql implements JoinInterface
{
    /**
     * @var string
     */
    protected $joinTable;

    /**
     * @var string
     */
    protected $joinCondition;

    /**
     * @var JoinAliasStorage
     */
    protected $aliasStorage;

    /**
     * @var bool
     */
    protected $isSqlOptional = true;

    /**
     * @Inject
     * @param JoinAliasStorage $aliasStorage
     */
    public function setAliasStorage(JoinAliasStorage $aliasStorage)
    {
        $this->aliasStorage = $aliasStorage;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->joinTable;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->aliasStorage->get($this->joinTable);
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->joinCondition;
    }
}
