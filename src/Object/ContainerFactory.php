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

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;

/**
 * Class ContainerFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class ContainerFactory
{
    /**
     * @var array
     */
    protected static $defaultDiObjects = [];

    /**
     * @param Cache|null $cache
     * @param array $overrideDiObjects
     * @return Container
     */
    public static function create(Cache $cache = null, array $overrideDiObjects = [])
    {
        $classMapping = static::overrideDiObjects($overrideDiObjects);
        if (!$cache instanceof Cache) {
            $cache = new ArrayCache;
        }
        return (new ContainerBuilder)
            ->addDefinitions($classMapping)
            ->useAnnotations(true)
            ->useAutowiring(true)
            ->setDefinitionCache($cache)
            ->build();
    }

    /**
     * @param array $overrideDiObjects
     * @return array
     */
    protected static function overrideDiObjects(array $overrideDiObjects)
    {
        $defaultDiObjects = require __DIR__ . '/../DiObjects.php';
        foreach ($overrideDiObjects as $type => $item) {
            $defaultDiObjects[$type] = $item;
        }
        return $defaultDiObjects;
    }
}
