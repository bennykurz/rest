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

namespace N86io\Rest\Tests\DomainObject\EntityInfo;

use Doctrine\Common\Cache\ArrayCache;
use N86io\Rest\Dependency;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity2;

/**
 * Class EntityInfoStorageTest
 * @package N86io\Rest\Tests\DomainObject\EntityInfo
 */
class EntityInfoStorageTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        /** @var EntityInfoFactory $entityInfoFactory */
        $entityInfoFactory = ObjectContainer::get(EntityInfoFactory::class);
        $fakeEntity2Info = $entityInfoFactory->buildEntityInfoFromClassName(FakeEntity2::class);

        /** @var ArrayCache $arrayCache */
        $cache = ObjectContainer::make(ArrayCache::class);
        $cache->save(md5(FakeEntity2::class), $fakeEntity2Info);
        Dependency::set('EntityInfoStorageCache', $cache);
        ObjectContainer::initialize();

        /** @var EntityInfoStorage $entityInfoStorage */
        $entityInfoStorage = ObjectContainer::get(EntityInfoStorage::class);
        $this->assertTrue($entityInfoStorage->get(FakeEntity1::class) instanceof EntityInfoInterface);
        $this->assertTrue($entityInfoStorage->get(FakeEntity1::class) instanceof EntityInfoInterface);
        $this->assertTrue($entityInfoStorage->get(FakeEntity2::class) instanceof EntityInfoInterface);
    }
}
