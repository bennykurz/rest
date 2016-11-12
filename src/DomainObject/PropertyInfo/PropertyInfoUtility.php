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

use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\Object\SingletonInterface;

/**
 * Class PropertyInfoUtility
 *
 * @author Viktor Firus <v@n86.io>
 */
class PropertyInfoUtility implements SingletonInterface
{
    /**
     * @param string $expression
     * @param string $tableAlias
     * @param string $aliasPlaceholder
     * @return string
     */
    public function placeTableAlias($expression, $tableAlias, $aliasPlaceholder = '')
    {
        if (preg_match('/%' . $aliasPlaceholder . '%([a-z_]*)%/', $expression) === 1) {
            return preg_replace('/%' . $aliasPlaceholder . '%([a-z_]*)%/', $tableAlias . '.$1', $expression);
        }
        return preg_replace('/%([a-z_]*)%/', $tableAlias . '.$1', $expression);
    }

    /**
     * @param string $className
     * @return bool
     * @throws \Exception
     */
    public function checkForAbstractEntitySubclass($className)
    {
        return is_subclass_of($className, AbstractEntity::class);
    }
}
