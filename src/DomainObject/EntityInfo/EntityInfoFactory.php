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

use DI\Container;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;
use N86io\Rest\Reflection\EntityClassReflection;

/**
 * Class EntityInfoFactory
 * @package N86io\Rest\DomainObject\EntityInfo
 */
class EntityInfoFactory implements EntityInfoFactoryInterface
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @Inject
     * @var PropertyInfoFactory
     */
    protected $propertyInfoFactory;

    /**
     * @Inject
     * @var PropertyInfoUtility
     */
    protected $propertyInfoUtility;

    /**
     * @Inject
     * @var EntityInfoConfLoader
     */
    protected $entityInfoConfLoader;

    /**
     * @param $className
     * @return EntityInfoInterface
     */
    public function buildEntityInfoFromClassName($className)
    {
        /** @var EntityClassReflection $entityClassRefl */
        $entityClassRefl = $this->container->make(EntityClassReflection::class, ['className' => $className]);
        $properties = $entityClassRefl->getProperties();
        $entityInfoConf = $this->loadEntityInfoConf($className, $entityClassRefl);
        $properties = $this->mergeProperties($properties, $entityInfoConf);
        $entityInfo = $this->createEntityInfo($className, $entityInfoConf);

        foreach ($properties as $name => $attributes) {
            $propertyInfo = $this->propertyInfoFactory->buildPropertyInfo($name, $attributes);
            $entityInfo->addPropertyInfo($propertyInfo);
        }

        if (!$entityInfo->hasUidPropertyInfo()) {
            // TODO: throw an exception
        }
        return $entityInfo;
    }

    /**
     * @param array $properties
     * @param array $entityInfoConf
     * @return array
     */
    protected function mergeProperties(array $properties, array $entityInfoConf)
    {
        if (!array_key_exists('properties', $entityInfoConf)) {
            return $properties;
        }
        return array_merge_recursive($entityInfoConf['properties'], $properties);
    }

    /**
     * @param string $className
     * @param EntityClassReflection $entityClassRefl
     * @return array
     */
    protected function loadEntityInfoConf($className, EntityClassReflection $entityClassRefl)
    {
        return $this->entityInfoConfLoader->loadSingle(
            $className,
            $entityClassRefl->getParentClasses()
        );
    }

    /**
     * @param string $className
     * @param array $entityInfoConf
     * @return EntityInfoInterface
     */
    protected function createEntityInfo($className, array $entityInfoConf)
    {
        $attributes = [];
        $keys = ['storage', 'table', 'mode'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $entityInfoConf)) {
                $attributes[$key] = $entityInfoConf[$key];
            }
        }
        $attributes['className'] = $className;
        return $this->container->make(EntityInfo::class, ['attributes' => $attributes]);
    }
}
