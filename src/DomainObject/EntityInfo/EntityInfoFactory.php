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

use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Reflection\EntityClassReflection;
use N86io\Rest\Utility\PropertyInfoUtility;

/**
 * Class EntityInfoFactory
 * @package N86io\Rest\DomainObject\EntityInfo
 */
class EntityInfoFactory
{
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
     * @param $className
     * @return EntityInfoInterface
     */
    public function buildEntityInfoFromClassName($className)
    {
        /** @var EntityClassReflection $entityClassRefl */
        $entityClassRefl = ObjectContainer::make(EntityClassReflection::class, ['className' => $className]);
        $classTags = $entityClassRefl->getClassTags();
        $properties = $entityClassRefl->getProperties();
        $this->setUndefinedPropertyAttributes($properties);
        $entityInfo = $this->createEntityInfo($className, $classTags);

        foreach ($properties as $name => $attributes) {
            $propertyInfo = $this->propertyInfoFactory->buildPropertyInfo($name, $attributes);
            $entityInfo->addPropertyInfo($propertyInfo);
        }

        if (!$entityInfo->hasUidPropertyInfo()) {
            $uidPropertyInfo = $this->propertyInfoFactory->buildUidPropertyInfo(!$entityInfo->hasResourceId());
            $entityInfo->addPropertyInfo($uidPropertyInfo);
        }
        return $entityInfo;
    }

    /**
     * @param string $className
     * @param array $classTags
     * @return EntityInfoInterface
     * @throws \Exception
     */
    protected function createEntityInfo($className, array $classTags)
    {
        $attributes = $classTags;
        $attributes['className'] = $className;
        return new EntityInfo($attributes);
    }

    /**
     * @param array $properties
     */
    protected function setUndefinedPropertyAttributes(array &$properties)
    {
        foreach ($properties as $propertyName => &$attributes) {
            if (!array_key_exists('resourcePropertyName', $attributes) &&
                !array_key_exists('sqlExpression', $attributes) && !array_key_exists('foreignField', $attributes) &&
                array_key_exists('type', $attributes)
            ) {
                $attributes['resourcePropertyName'] = $this->propertyInfoUtility->convertPropertyName($propertyName);
            }
        }
    }
}
