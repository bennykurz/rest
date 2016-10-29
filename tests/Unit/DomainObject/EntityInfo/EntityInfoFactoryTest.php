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
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity4;

/**
 * Class EntityInfoFactoryTest
 * @package N86io\Rest\Tests\DomainObject\EntityInfo
 */
class EntityInfoFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityInfoFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = ObjectContainer::get(EntityInfoFactory::class);
    }

    public function test()
    {
        /** @var EntityInfo $actual */
        $actual = $this->factory->buildEntityInfoFromClassName(FakeEntity1::class);
        $this->assertEquals('N86io\Rest\Tests\DomainObject\FakeEntity1', $actual->getClassName());
        $this->assertEquals('fakeId', $actual->getPropertyInfo('fakeId')->getName());
        $this->assertEquals('dateTimeTimestamp', $actual->mapResourcePropertyName('date_time_timestamp'));
        $actual = $this->factory->buildEntityInfoFromClassName(FakeEntity4::class);
        $this->assertEquals('fakeId', $actual->getPropertyInfo('fakeId')->getName());
    }
}
