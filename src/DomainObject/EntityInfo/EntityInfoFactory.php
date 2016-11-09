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
use N86io\Rest\DomainObject\PropertyInfo\EnableFieldPropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;
use N86io\Rest\Reflection\EntityClassReflection;

/**
 * Class EntityInfoFactory
 *
 * @author Viktor Firus <v@n86.io>
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
     * @Inject
     * @var EnableFieldPropertyInfoFactory
     */
    protected $enablFieldPropInfFac;

    /**
     * @param string $className
     * @return EntityInfoInterface
     * @throws \Exception
     */
    public function buildEntityInfoFromClassName($className)
    {
        /** @var EntityClassReflection $entityClassRefl */
        $entityClassRefl = $this->container->make(EntityClassReflection::class, ['className' => $className]);
        $properties = $entityClassRefl->getProperties();
        $entityInfoConf = $this->loadEntityInfoConf($className, $entityClassRefl);
        $properties = $this->mergeProperties($properties, $entityInfoConf);
        $properties = $this->setUndefinedPropertiesAttributes($properties);
        $entityInfo = $this->createEntityInfo($className, $entityInfoConf);
        foreach ($properties as $name => $attributes) {
            $propertyInfo = $this->propertyInfoFactory->buildPropertyInfo($name, $attributes);
            $entityInfo->addPropertyInfo($propertyInfo);
        }

        if (array_key_exists('enableFields', $entityInfoConf)) {
            foreach ($entityInfoConf['enableFields'] as $type => $enableField) {
                $entityInfo->addPropertyInfo($this->enablFieldPropInfFac->build($type, $enableField));
            }
        }

        if (!$entityInfo->hasUidPropertyInfo()) {
            throw new \Exception('It is necessary to define a field for unique id.');
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
        $keys = ['repository', 'table', 'mode', 'enableFields'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $entityInfoConf)) {
                $attributes[$key] = $entityInfoConf[$key];
            }
        }
        $attributes['className'] = $className;
        return $this->container->make(EntityInfo::class, ['attributes' => $attributes]);
    }

    /**
     * @param array $properties
     * @return array
     */
    protected function setUndefinedPropertiesAttributes(array $properties)
    {
        foreach ($properties as $propertyName => &$attributes) {
            if (!array_key_exists('resourcePropertyName', $attributes) &&
                !array_key_exists('sqlExpression', $attributes) &&
                $attributes['type'] !== '__dynamic'
            ) {
                $attributes['resourcePropertyName'] = $this->convertPropertyName($propertyName);
            }
        }
        return $properties;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function convertPropertyName($string)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_$1', $string));
    }
}
