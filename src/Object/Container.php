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
use Webmozart\Assert\Assert;

/**
 * Class Container
 *
 * @author Viktor Firus <v@n86.io>
 */
class Container
{
    /**
     * @var array
     */
    protected static $singletonInstances = [];

    /**
     * @var DefinitionFactory
     */
    protected static $definitionFactory;

    /**
     * @var array
     */
    protected static $classMapping;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        static::$singletonInstances[self::class] = $this;
    }

    public static function initializeContainer(Cache $cache, array $classMapping = [])
    {
        if (static::$definitionFactory instanceof Cache) {
            throw new \Exception('Cache for Container already exist.');
        }
        static::$definitionFactory = new DefinitionFactory($cache);
        if (is_array(static::$classMapping)) {
            throw new \Exception('Class mapping for Container already exist.');
        }
        static::$classMapping = $classMapping;
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return object
     */
    public function get($className, $parameters = [])
    {
        return static::makeInstance($className, $parameters);
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return object
     */
    public static function makeInstance($className, $parameters = [])
    {
        Assert::isInstanceOf(static::$definitionFactory, DefinitionFactory::class, 'Container should be initialized.');
        $className = static::mapClassName($className);
        $className = static::resolveInterface($className);
        if (array_key_exists($className, static::$singletonInstances)) {
            return static::$singletonInstances[$className];
        }
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class "' . $className . '" not found.');
        }
        if ($className === Definition::class || $className === DefinitionFactory::class) {
            throw new \InvalidArgumentException('Not allowed to instantiate "' . $className . '".');
        }

        $definition = static::$definitionFactory->get($className);

        $reflectionClass = new ReflectionClass($definition->getClassName());
        $instance = $reflectionClass->newInstanceArgs($parameters);

        $injections = $definition->getInjections();
        foreach ($injections as $injection) {
            $reflectionInstance = new ReflectionClass($instance);
            $reflectionProperty = $reflectionInstance->getProperty($injection['propertyName']);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue(
                $instance,
                static::makeInstance($injection['className'])
            );
        }

        if ($definition->hasInitializeMethod()) {
            $instance->initializeObject();
        }

        if ($definition->isSingleton()) {
            static::$singletonInstances[$className] = $instance;
        }

        return $instance;
    }

    /**
     * @param string $className
     * @return string
     */
    protected static function mapClassName($className)
    {
        if (array_key_exists($className, static::$classMapping)) {
            return static::$classMapping[$className];
        }
        return $className;
    }

    /**
     * @param string $interfaceName
     * @return string
     */
    protected static function resolveInterface($interfaceName)
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
