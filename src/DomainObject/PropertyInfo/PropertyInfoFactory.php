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

use DI\Container;
use N86io\Rest\DomainObject\PropertyInfo;
use N86io\Rest\DomainObject\PropertyInfo\Factory\FactoryInterface;

/**
 * Class PropertyInfoFactory
 * @package N86io\Rest\DomainObject\PropertyInfo
 */
class PropertyInfoFactory
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @var FactoryInterface[]
     */
    protected $factories = [
        PropertyInfo\Factory\DynamicPhp::class,
        PropertyInfo\Factory\DynamicSql::class,
        PropertyInfo\Factory\Relation::class,
        PropertyInfo\Factory\RelationOnForeignField::class
    ];

    /**
     * @param string $name
     * @param array $attributes
     * @return PropertyInfoInterface
     */
    public function buildPropertyInfo($name, array $attributes)
    {
        foreach ($this->factories as $factoryClassName) {
            /** @var FactoryInterface $factory */
            $factory = $this->container->get($factoryClassName);
            if ($factory->check($attributes)) {
                return $factory->build($name, $attributes);
            }
        }
        return $this->container->make(PropertyInfo\Common::class, ['name' => $name, 'attributes' => $attributes]);
    }

    /**
     * @param string $factory
     */
    public function registerPropertyInfoFactory($factory)
    {
        if (!is_subclass_of($factory, FactoryInterface::class)) {
            throw new \InvalidArgumentException('"' . $factory . '" must implemented "' .
                FactoryInterface::class . '".');
        }
        $this->factories[] = $factory;
    }
}
