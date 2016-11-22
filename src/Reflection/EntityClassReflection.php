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

use N86io\Reflection\ReflectionClass;
use N86io\Rest\DomainObject\AbstractEntity;

/**
 * Class EntityClassReflection
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityClassReflection
{
    /**
     * @inject
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
    protected $properties;

    /**
     * @var array
     */
    protected $parentClasses = [];

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
    public function getProperties()
    {
        $this->load();
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getParentClasses()
    {
        $this->load();
        return $this->parentClasses;
    }

    protected function load()
    {
        if ($this->properties) {
            return;
        }
        $this->createInheritValues();
        $this->properties = $this->getPropertiesAttributes();
        $methodsAttributes = $this->getMethodsAttributes();
        $this->mergeMethodsAttributesIntoProperties($methodsAttributes);
    }

    protected function createInheritValues()
    {
        $this->classSummary = $this->reflectionClass->getParsedDocComment()->getSummary();
        $this->classDescription = $this->reflectionClass->getParsedDocComment()->getDescription();

        $class = $this->reflectionClass;
        while (($class = $class->getParentClass())) {
            if (!$class instanceof ReflectionClass) {
                break;
            }
            if ($class->getName() === AbstractEntity::class) {
                break;
            }
            if (trim($this->classSummary) === '') {
                $this->classSummary = $class->getParsedDocComment()->getSummary();
            }
            if (trim($this->classDescription) === '') {
                $this->classDescription = $class->getParsedDocComment()->getDescription();
            }
            array_unshift($this->parentClasses, $class->getName());
        }
    }

    /**
     * @param array $methodsAttributes
     */
    protected function mergeMethodsAttributesIntoProperties(array $methodsAttributes)
    {
        foreach ($methodsAttributes as $propertyName => $methodAttributes) {
            if (isset($this->properties[$propertyName])) {
                continue;
            }
            $propertyAttributes = &$this->properties[$propertyName];
            $propertyAttributes['type'] = '__dynamic';
            if (isset($methodAttributes['getter'])) {
                $propertyAttributes['getter'] = $methodAttributes['getter'];
            }
            if (isset($methodAttributes['setter'])) {
                $propertyAttributes['setter'] = $methodAttributes['setter'];
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
            if ($method->getDeclaringClass()->getName() === AbstractEntity::class ||
                $method->isProtected() || $method->isPrivate() ||
                !$this->methodNameUtility->isGetterOrSetter($method->getName())
            ) {
                continue;
            }
            $propertyName = $this->methodNameUtility->createPropertyNameFromMethod($method->getName());
            if ($this->methodNameUtility->isGetter($method->getName())) {
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
            if ($property->getDeclaringClass()->getName() === AbstractEntity::class) {
                continue;
            }
            $attr = &$propertiesAttr[$property->getName()];
            $attr['type'] = current($property->getParsedDocComment()->getTags()['var']);
            if ($this->classExists($attr['type']) && $attr['type'][0] === '\\') {
                $attr['type'] = substr($attr['type'], 1);
            }
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

    /**
     * @param string $className
     * @return bool
     */
    protected function classExists($className)
    {
        return class_exists(substr($className, 0, strlen($className) - 2)) || class_exists($className);
    }
}
