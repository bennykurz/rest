<?php declare(strict_types = 1);
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

use N86io\Di\ContainerInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;
use N86io\Rest\Reflection\EntityClassReflection;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class EntityInfoFactory implements EntityInfoFactoryInterface
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inject
     * @var PropertyInfoFactory
     */
    protected $propertyInfoFactory;

    /**
     * @inject
     * @var PropertyInfoUtility
     */
    protected $propertyInfoUtility;

    /**
     * @inject
     * @var EntityInfoConfLoader
     */
    protected $entityInfoConfLoader;

    /**
     * @param string $className
     *
     * @return EntityInfoInterface
     * @throws \Exception
     */
    public function buildEntityInfoFromClassName(string $className): EntityInfoInterface
    {
        $entityClassReflection = $this->container->get(EntityClassReflection::class, $className);
        $properties = $entityClassReflection->getProperties();

        list(
            $propertiesConf,
            $connector,
            $table,
            $mode,
            $enableFields,
            $joins
            ) = $this->loadEntityInfoConf($className, $entityClassReflection);

        $properties = $this->mergeProperties($properties, $propertiesConf);
        $properties = $this->setUndefinedPropertiesAttributes($properties);

        $entityInfo = $this->container->get(EntityInfo::class, $className, $table, $mode, $connector);

        foreach ($properties as $name => $attributes) {
            $attributes['entityClassName'] = $entityInfo->getClassName();
            $type = $attributes['type'];
            unset($attributes['type']);
            $propertyInfo = $this->propertyInfoFactory->build($name, $type, $attributes);
            $entityInfo->addPropertyInfo($propertyInfo);
        }

        foreach ($enableFields as $type => $enableField) {
            $entityInfo->addPropertyInfo($this->propertyInfoFactory->buildEnableField(
                $type,
                $enableField,
                $entityInfo->getClassName()
            ));
        }

        foreach ($joins as $alias => $attributes) {
            /** @var JoinInterface $join */
            $join = $this->container->get(JoinInterface::class, $alias, $attributes['table'], $attributes['condition']);
            $entityInfo->addJoin($join);
        }

        if (!$entityInfo->hasUidPropertyInfo()) {
            throw new \Exception('It is necessary to define a field for unique id.');
        }

        return $entityInfo;
    }

    /**
     * @param array $properties
     * @param array $propertiesConf
     *
     * @return array
     */
    protected function mergeProperties(array $properties, array $propertiesConf): array
    {
        if (empty($propertiesConf)) {
            return $properties;
        }

        return array_merge_recursive($propertiesConf, $properties);
    }

    /**
     * @param string                $className
     * @param EntityClassReflection $entityClassReflection
     *
     * @return array
     */
    protected function loadEntityInfoConf(string $className, EntityClassReflection $entityClassReflection): array
    {
        $entityInfoConf = $this->entityInfoConfLoader->loadSingle(
            $className,
            $entityClassReflection->getParentClasses()
        );

        return [
            isset($entityInfoConf['properties']) ? $entityInfoConf['properties'] : [],
            isset($entityInfoConf['connector']) ? $entityInfoConf['connector'] : '',
            isset($entityInfoConf['table']) ? $entityInfoConf['table'] : '',
            isset($entityInfoConf['mode']) ? $entityInfoConf['mode'] : [],
            isset($entityInfoConf['enableFields']) ? $entityInfoConf['enableFields'] : [],
            isset($entityInfoConf['joins']) ? $entityInfoConf['joins'] : []
        ];
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    protected function setUndefinedPropertiesAttributes(array $properties): array
    {
        foreach ($properties as $propertyName => &$attributes) {
            if (empty($attributes['resourcePropertyName']) &&
                empty($attributes['sqlExpression']) &&
                $attributes['type'] !== '__dynamic'
            ) {
                $attributes['resourcePropertyName'] = $this->convertPropertyName($propertyName);
            }
        }

        return $properties;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function convertPropertyName(string $string): string
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_$1', $string));
    }
}
