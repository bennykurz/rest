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

/**
 * Class DynamicPhp
 * @package N86io\Rest\DomainObject\PropertyInfo\Factory
 */
class DynamicPhp extends AbstractFactory
{
    /**
     * @param string $name
     * @param array $attributes
     * @return \N86io\Rest\DomainObject\PropertyInfo\DynamicPhp
     */
    public function build($name, array $attributes)
    {
        return $this->container->make(
            \N86io\Rest\DomainObject\PropertyInfo\DynamicPhp::class,
            [
                'name' => $name,
                'attributes' => $attributes
            ]
        );
    }

    /**
     * @param array $attributes
     * @return boolean
     */
    public function check(array $attributes)
    {
        return $attributes['type'] === '__dynamic';
    }
}
