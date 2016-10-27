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
use Interop\Container\ContainerInterface;

/**
 * Class RestObjectManager
 * @package N86io\Rest
 */
class RestObjectManager
{
    /**
     * @var Container
     */
    private static $container;

    /**
     * RestObjectManager constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache = null)
    {
        if (!self::$container) {
            $containerBuilder = (new ContainerBuilder)
                ->addDefinitions([ContainerInterface::class => \DI\get(Container::class)])
                ->useAnnotations(true)
                ->useAutowiring(true);
            if ($cache) {
                self::$container = $containerBuilder->setDefinitionCache($cache)->build();
                return;
            }
            self::$container = $containerBuilder->setDefinitionCache(new ArrayCache)->build();
        }
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return object
     */
    public function get($className, array $parameters = [])
    {
        if (empty($parameters)) {
            return self::$container->get($className);
        }
        return self::$container->make($className, $parameters);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return self::$container;
    }
}
