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

namespace N86io\Rest\Object;

use Doctrine\Common\Cache\Cache;
use N86io\Reflection\ReflectionClass;

/**
 * Class Container
 *
 * @author Viktor Firus <v@n86.io>
 */
class Container implements SingletonInterface
{
    /**
     * @var array
     */
    protected $singletonInstances = [];

    /**
     * @var DefinitionFactory
     */
    protected $definitionFactory;

    /**
     * @var array
     */
    protected $classMapping;

    /**
     * Container constructor.
     * @param Cache $cache
     * @param array $classMapping
     */
    public function __construct(Cache $cache, array $classMapping = [])
    {
        $this->definitionFactory = new DefinitionFactory($cache);
        $this->classMapping = $classMapping;
        $this->singletonInstances[self::class] = $this;
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return object
     */
    public function get($className, $parameters = [])
    {
        $className = $this->mapClassName($className);
        $className = $this->resolveInterface($className);
        if (array_key_exists($className, $this->singletonInstances)) {
            return $this->singletonInstances[$className];
        }
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class "' . $className . '" not found.');
        }
        if ($className === Definition::class || $className === DefinitionFactory::class) {
            throw new \InvalidArgumentException('Not allowed to instantiate "' . $className . '".');
        }

        $definition = $this->definitionFactory->get($className);

        $reflectionClass = new ReflectionClass($definition->getClassName());
        $instance = $reflectionClass->newInstanceArgs($parameters);

        $injections = $definition->getInjections();
        foreach ($injections as $injection) {
            $reflectionInstance = new ReflectionClass($instance);
            $reflectionProperty = $reflectionInstance->getProperty($injection['propertyName']);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue(
                $instance,
                $this->get($injection['className'])
            );
        }

        if ($definition->isSingleton()) {
            $this->singletonInstances[$className] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $className
     * @return string
     */
    protected function mapClassName($className)
    {
        if (array_key_exists($className, $this->classMapping)) {
            return $this->classMapping[$className];
        }
        return $className;
    }

    /**
     * @param string $interfaceName
     * @return string
     */
    protected function resolveInterface($interfaceName)
    {
        if (interface_exists($interfaceName)) {
            $className = substr($interfaceName, 0, strlen($interfaceName) - 9);
            if (!class_exists($className) || !is_subclass_of($className, $interfaceName)) {
                throw new \InvalidArgumentException('Can\'t resolve interface "' . $interfaceName . '".');
            }
            return $className;
        }
        return $interfaceName;
    }
}
