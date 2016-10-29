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

use Interop\Container\ContainerInterface;
use N86io\Rest\DomainObject\PropertyInfo;

/**
 * Class PropertyInfoFactory
 * @package N86io\Rest\DomainObject\PropertyInfo
 */
class PropertyInfoFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $factories = [
        PropertyInfo\Factory\DynamicPhp::class,
        PropertyInfo\Factory\DynamicSql::class,
        PropertyInfo\Factory\Relation::class,
        PropertyInfo\Factory\RelationOnForeignField::class
    ];

    /**
     * @Inject
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @return PropertyInfoInterface
     */
    public function buildPropertyInfo($name, array $attributes)
    {
        foreach ($this->factories as $factoryClassName) {
            /** @var PropertyInfo\Factory\FactoryInterface $factory */
            $factory = $this->container->get($factoryClassName);
            if ($factory->check($attributes)) {
                return $factory->build($name, $attributes);
            }
        }
        return new PropertyInfo\Common($name, $attributes);
    }

    /**
     * @param $isResourceId bool
     * @return Common
     */
    public function buildUidPropertyInfo($isResourceId)
    {
        $attributes = [
            'type' => 'int',
            'resourcePropertyName' => 'uid',
            'hide' => true,
            'resourceId' => $isResourceId
        ];
        return new PropertyInfo\Common('_uid', $attributes);
    }
}
