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

namespace N86io\Rest\Reflection;

use DI\Container;
use N86io\Reflection\ReflectionClass;
use N86io\Rest\DomainObject\AbstractEntity;

/**
 * Class EntityClassReflection
 * @package N86io\Rest\Reflection
 * @Injectable(scope="prototype")
 */
class EntityClassReflection
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @Inject
     * @var TagUtility
     */
    protected $tagUtility;

    /**
     * @Inject
     * @var MethodNameUtility
     */
    protected $methodNameUtility;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var string
     */
    protected $classSummary;

    /**
     * @var string
     */
    protected $classDescription;

    /**
     * @var array
     */
    protected $classTags;

    /**
     * @var array
     */
    protected $properties;

    /**
     * EntityClassReflection constructor.
     * @param string $className
     * @throws \Exception
     */
    public function __construct($className)
    {
        if (!is_subclass_of($className, AbstractEntity::class)) {
            throw new \InvalidArgumentException(
                $className . ' should be a subclass of ' . AbstractEntity::class
            );
        }
        $this->reflectionClass = new ReflectionClass($className);
    }

    /**
     * @return string
     */
    public function getClassSummary()
    {
        $this->load();
        return $this->classSummary;
    }

    /**
     * @return string
     */
    public function getClassDescription()
    {
        $this->load();
        return $this->classDescription;
    }

    /**
     * @return array
     */
    public function getClassTags()
    {
        $this->load();
        return $this->classTags;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $this->load();
        return $this->properties;
    }

    protected function load()
    {
        if ($this->properties) {
            return;
        }
        $this->classSummary = $this->reflectionClass->getParsedDocComment()->getSummary();
        $this->classDescription = $this->reflectionClass->getParsedDocComment()->getDescription();
        $this->classTags = $this->reflectionClass->getParsedDocComment()->getTags();
        $this->properties = $this->getPropertiesAttributes();
        $methodsAttributes = $this->getMethodsAttributes();
        $this->mergeMethodsAttributesIntoProperties($methodsAttributes);
        $this->classTags = $this->tagUtility->evaluateTagList($this->classTags);
        $this->properties = $this->tagUtility->evaluatePropertyList($this->properties);
        $this->mergeWithParentClass();
    }

    protected function mergeWithParentClass()
    {
        if (($parentClass = $this->reflectionClass->getParentClass()->getName()) !== AbstractEntity::class) {
            /** @var EntityClassReflection $parent */
            $parent = $this->container->make(EntityClassReflection::class, ['className' => $parentClass]);
            if (trim($this->classSummary) === '') {
                $this->classSummary = $parent->getClassSummary();
            }
            if ($this->classDescription === '') {
                $this->classDescription = $parent->getClassDescription();
            }
            if (!empty($parentClassTags = $parent->getClassTags())) {
                $this->classTags = $this->tagUtility->mergeTagList($parentClassTags, $this->classTags);
            }
            if (!empty($parentProperties = $parent->getProperties())) {
                $this->properties = $this->tagUtility->mergePropertyList($parentProperties, $this->properties);
            }
        }
    }

    /**
     * @param array $methodsAttributes
     */
    protected function mergeMethodsAttributesIntoProperties(array $methodsAttributes)
    {
        foreach ($methodsAttributes as $propertyName => $methodAttributes) {
            if (array_key_exists($propertyName, $this->properties)) {
                continue;
            }
            $propertyAttributes = &$this->properties[$propertyName];
            if (empty($propertyAttributes)) {
                $propertyAttributes = [
                    'type' => '__dynamic',
                    'position' => $methodAttributes['position'],
                    'outputLevel' => $methodAttributes['outputLevel'],
                    'getter' => $methodAttributes['getter'],
                    'setter' => $methodAttributes['setter']
                ];
            }
        }
    }

    /**
     * @return array
     */
    protected function getMethodsAttributes()
    {
        $methodsAttr = [];
        $methods = $this->reflectionClass->getMethods();
        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() !== $this->reflectionClass->getName() ||
                $method->isProtected() || $method->isPrivate() ||
                !$this->methodNameUtility->isGetterOrSetter($method->getName())
            ) {
                continue;
            }
            $propertyName = $this->methodNameUtility->createPropertyNameFromMethod($method->getName());
            $attr = &$methodsAttr[$propertyName];
            if (!is_array($attr)) {
                $attr = [];
            }
            if ($this->methodNameUtility->isGetter($method->getName())) {
                $attr = array_merge($method->getParsedDocComment()->getTags(), $attr);
                $methodsAttr[$propertyName]['getter'] = $method->getName();
            }
            if ($this->methodNameUtility->isSetter($method->getName())) {
                $methodsAttr[$propertyName]['setter'] = $method->getName();
                continue;
            }
        }
        return $methodsAttr;
    }

    /**
     * @return array
     */
    protected function getPropertiesAttributes()
    {
        $propertiesAttr = [];
        $properties = $this->reflectionClass->getProperties();
        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->getName() !== $this->reflectionClass->getName()) {
                continue;
            }
            $attr = &$propertiesAttr[$property->getName()];
            $attr = $property->getParsedDocComment()->getTags();
            $attr['type'] = $attr['var'];
            unset($attr['var'], $attr['getter'], $attr['setter']);
            if ($property->hasGetter()) {
                $attr['getter'] = $property->getGetter()->getName();
            }
            if ($property->hasSetter()) {
                $attr['setter'] = $property->getSetter()->getName();
            }
        }
        return $propertiesAttr;
    }
}
