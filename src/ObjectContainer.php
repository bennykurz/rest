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

namespace N86io\Rest;

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;

/**
 * Class ObjectContainer
 * @package N86io\Rest
 */
class ObjectContainer
{
    /**
     * @var Container
     */
    private static $container;

    /**
     * @var Cache
     */
    private static $cache;

    /**
     * @param Cache $cache
     */
    public static function setCache(Cache $cache)
    {
        static::$cache = $cache;
        static::$container = null;
    }

    /**
     * @param $className
     * @return mixed
     */
    public static function get($className)
    {
        return static::getContainer()->get($className);
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return mixed
     */
    public static function make($className, array $parameters = [])
    {
        return static::getContainer()->make($className, $parameters);
    }

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (!static::$container) {
            static::initialize();
        }
        return static::$container;
    }

    /**
     * ObjectContainer constructor.
     */
    public static function initialize()
    {
        $containerBuilder = (new ContainerBuilder)
            ->addDefinitions(Dependency::get())
            ->useAnnotations(true)
            ->useAutowiring(true);
        if (static::$cache) {
            static::$container = $containerBuilder->setDefinitionCache(static::$cache)->build();
            return;
        }
        static::$container = $containerBuilder->setDefinitionCache(new ArrayCache)->build();
    }
}
