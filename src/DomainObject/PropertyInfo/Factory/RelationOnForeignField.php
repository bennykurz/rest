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

namespace N86io\Rest\DomainObject\PropertyInfo\Factory;

use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;

/**
 * Class RelationOnForeignField
 *
 * @author Viktor Firus <v@n86.io>
 */
class RelationOnForeignField extends AbstractFactory
{
    /**
     * @inject
     * @var PropertyInfoUtility
     */
    protected $propertyInfoUtility;

    /**
     * @param string $name
     * @param array $attributes
     * @return \N86io\Rest\DomainObject\PropertyInfo\RelationOnForeignField
     */
    public function build($name, array $attributes)
    {
        return $this->container->get(
            \N86io\Rest\DomainObject\PropertyInfo\RelationOnForeignField::class,
            [$name, $attributes]
        );
    }

    /**
     * @param array $attributes
     * @return boolean
     */
    public function check(array $attributes)
    {
        if (empty($attributes['foreignField'])) {
            return false;
        }
        return $this->propertyInfoUtility->checkForAbstractEntitySubclass($attributes['type']);
    }
}
