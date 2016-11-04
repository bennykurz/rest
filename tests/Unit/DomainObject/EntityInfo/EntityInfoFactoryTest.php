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

use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactoryInterface;
use N86io\Rest\Object\ContainerFactory;
use N86io\Rest\Service\Configuration;
use N86io\Rest\Tests\DomainObject\FakeEntity2;
use N86io\Rest\Tests\DomainObject\FakeEntity4;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityInfoFactoryTest
 * @package N86io\Rest\Tests\DomainObject\EntityInfo
 */
class EntityInfoFactoryTest extends UnitTestCase
{
    public function test()
    {
        /** @var EntityInfoFactoryInterface $factory */
        $factory = static::$container->get(EntityInfoFactoryInterface::class);

        /** @var EntityInfo $actual */
        $actual = $factory->buildEntityInfoFromClassName(FakeEntity2::class);
        $this->assertEquals('N86io\Rest\Tests\DomainObject\FakeEntity2', $actual->getClassName());
        $this->assertEquals('fakeId', $actual->getPropertyInfo('fakeId')->getName());
        $this->assertTrue($actual->getPropertyInfo('fakeId')->isResourceId());
        $this->assertEquals('dateTimeTimestamp', $actual->mapResourcePropertyName('date_time_timestamp'));
        $this->assertEquals(102, $actual->getPropertyInfo('array')->getPosition());

        $actual = $factory->buildEntityInfoFromClassName(FakeEntity4::class);
        $this->assertEquals('fakeId', $actual->getPropertyInfo('fakeId')->getName());
        $this->assertTrue($actual->getPropertyInfo('string')->isOrdering());
    }

    public function testWithoutConfFile()
    {
        static::$container = ContainerFactory::create();
        /** @var EntityInfoFactoryInterface $factory */
        $factory = static::$container->get(EntityInfoFactoryInterface::class);
        $actual = $factory->buildEntityInfoFromClassName(FakeEntity4::class);
        $this->assertEquals('fakeId', $actual->getPropertyInfo('fakeId')->getName());
        $this->assertFalse($actual->getPropertyInfo('string')->isOrdering());
    }
}
