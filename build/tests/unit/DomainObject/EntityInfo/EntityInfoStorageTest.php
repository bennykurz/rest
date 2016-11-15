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

namespace N86io\Rest\Tests\Unit\DomainObject\EntityInfo;

use Doctrine\Common\Cache\ArrayCache;
use Mockery\MockInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityInfoStorageTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoStorageTest extends UnitTestCase
{
    public function test()
    {
        $cache = new ArrayCache;
        $arrayCache = new ArrayCache;

        $entityInfoStorage = new EntityInfoStorage;

        $this->inject($entityInfoStorage, 'cache', $cache);
        $this->inject($entityInfoStorage, 'arrayCache', $arrayCache);

        $factoryMock = $this->getEntityInfoFactoryMock();

        $this->inject($entityInfoStorage, 'entityInfoFactory', $factoryMock);

        $entity2Info = $factoryMock->buildEntityInfoFromClassName('Entity2');
        $cache->save(md5('Entity2'), $entity2Info);

        $this->assertTrue($entityInfoStorage->get('Entity1') instanceof EntityInfoInterface);
        $this->assertTrue($entityInfoStorage->get('Entity1') instanceof EntityInfoInterface);
        $this->assertTrue($entityInfoStorage->get('Entity2') instanceof EntityInfoInterface);
    }

    /**
     * @return MockInterface|EntityInfoFactory
     */
    protected function getEntityInfoFactoryMock()
    {
        return \Mockery::mock(EntityInfoFactory::class)
            ->shouldReceive('buildEntityInfoFromClassName')->with('Entity1')
            ->andReturn(\Mockery::mock(EntityInfoInterface::class))->getMock()
            ->shouldReceive('buildEntityInfoFromClassName')->with('Entity2')
            ->andReturn(\Mockery::mock(EntityInfoInterface::class))->getMock();
    }
}
