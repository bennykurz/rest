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

use N86io\Di\Singleton;
use N86io\Rest\Cache\EntityInfoStorageArrayCacheInterface;
use N86io\Rest\Cache\EntityInfoStorageCacheInterface;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class EntityInfoStorage implements Singleton
{
    /**
     * @inject
     * @var EntityInfoFactoryInterface
     */
    protected $entityInfoFactory;

    /**
     * @inject
     * @var EntityInfoStorageCacheInterface
     */
    protected $cache;

    /**
     * @inject
     * @var EntityInfoStorageArrayCacheInterface
     */
    protected $arrayCache;

    /**
     * @param string $modelClassName
     *
     * @return EntityInfoInterface
     */
    public function get(string $modelClassName): EntityInfoInterface
    {
        $cacheId = $this->getCacheId($modelClassName);
        if (!$this->arrayCache->contains($cacheId)) {
            $entityInfo = $this->getFromCache($modelClassName);
            $this->arrayCache->save($cacheId, $entityInfo);

            return $entityInfo;
        }

        return $this->arrayCache->fetch($cacheId);
    }

    /**
     * @param string $modelClassName
     *
     * @return EntityInfoInterface
     */
    protected function getFromCache(string $modelClassName): EntityInfoInterface
    {
        $cacheId = $this->getCacheId($modelClassName);
        if (!$this->cache->contains($cacheId)) {
            $entityInfo = $this->entityInfoFactory->buildEntityInfoFromClassName($modelClassName);
            $this->cache->save($cacheId, $entityInfo);

            return $entityInfo;
        }

        return $this->cache->fetch($cacheId);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function getCacheId(string $string): string
    {
        return md5($string);
    }
}
