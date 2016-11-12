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
 * Class Relation
 *
 * @author Viktor Firus <v@n86.io>
 */
class Relation extends AbstractFactory
{
    /**
     * @inject
     * @var PropertyInfoUtility
     */
    protected $propertyInfoUtility;

    /**
     * @param string $name
     * @param array $attributes
     * @return \N86io\Rest\DomainObject\PropertyInfo\Relation
     */
    public function build($name, array $attributes)
    {
        return $this->container->get(
            \N86io\Rest\DomainObject\PropertyInfo\Relation::class,
            [$name, $attributes]
        );
    }

    /**
     * @param array $attributes
     * @return boolean
     */
    public function check(array $attributes)
    {
        if (!empty($attributes['foreignField'])) {
            return false;
        }
        return $this->checkForAbstractEntitySubclass($attributes['type']);
    }

    /**
     * @param string $className
     * @return bool
     */
    protected function checkForAbstractEntitySubclass($className)
    {
        return ($this->propertyInfoUtility->checkForAbstractEntitySubclass($className) ||
            $this->propertyInfoUtility->checkForAbstractEntitySubclass(
                substr($className, 0, strlen($className) - 2)
            ));
    }
}
