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

use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoConfLoader;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;
use N86io\Rest\Object\Container;
use N86io\Rest\Reflection\EntityClassReflection;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityInfoFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoFactoryTest extends UnitTestCase
{
    /**
     * @var EntityInfoFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new EntityInfoFactory;
        $this->inject($this->factory, 'entityInfoConfLoader', $this->createEntityInfoConfLoaderMock1());
        $this->inject($this->factory, 'propertyInfoUtility', $this->createPropertyInfoUtilityMock());
        $this->inject($this->factory, 'propertyInfoFactory', $this->createPropertyInfoFactoryMock());
        $this->inject($this->factory, 'container', $this->createContainerMock());
    }

    public function test()
    {
        $this->factory->buildEntityInfoFromClassName('Entity1');

        $this->inject($this->factory, 'entityInfoConfLoader', $this->createEntityInfoConfLoaderMock2());
        $this->factory->buildEntityInfoFromClassName('Entity1');
    }

    public function testNoUidDefined()
    {
        $this->setExpectedException(\Exception::class);
        $this->factory->buildEntityInfoFromClassName('Entity2');
    }

    protected function createPropertyInfoFactoryMock()
    {
        $mock = \Mockery::mock(PropertyInfoFactory::class);
        $mock->shouldReceive('build')->withAnyArgs()->andReturn(\Mockery::mock(PropertyInfoInterface::class));
        $mock->shouldReceive('buildEnableField')->withAnyArgs()->andReturn(
            \Mockery::mock(PropertyInfoInterface::class)
        );
        return $mock;
    }

    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(EntityClassReflection::class, ['Entity1'])
            ->andReturn($this->createEntityClassReflectionMock1());
        $mock->shouldReceive('get')->with(EntityClassReflection::class, ['Entity2'])
            ->andReturn($this->createEntityClassReflectionMock2());
        $mock->shouldReceive('get')->with(EntityInfo::class, [
            [
                'className' => 'Entity1',
                'table' => 'fake_table',
                'enableFields' => ['disabled' => 'disableFieldName']
            ]
        ])->andReturn($this->createEntityInfoMock1());
        $mock->shouldReceive('get')->with(
            EntityInfo::class,
            [['className' => 'Entity2']]
        )->andReturn($this->createEntityInfoMock2());

        return $mock;
    }

    /**
     * build mock for EntityInfo for class Entity1
     */
    protected function createEntityInfoMock1()
    {
        $mock = \Mockery::mock(EntityInfo::class);
        $mock->shouldReceive('addPropertyInfo')->withAnyArgs();
        $mock->shouldReceive('hasUidPropertyInfo')->andReturn(true);
        $mock->shouldReceive('getClassName')->withAnyArgs()->andReturn('ClassName');

        return $mock;
    }

    /**
     * build mock for EntityInfo for class Entity2
     */
    protected function createEntityInfoMock2()
    {
        $mock = \Mockery::mock(EntityInfo::class);
        $mock->shouldReceive('addPropertyInfo')->withAnyArgs();
        $mock->shouldReceive('hasUidPropertyInfo')->andReturn(false);
        $mock->shouldReceive('hasResourceId')->andReturn(false);
        $mock->shouldReceive('getClassName')->withAnyArgs()->andReturn('ClassName');

        return $mock;
    }

    /**
     * build mock for reflection of Entity1
     */
    protected function createEntityClassReflectionMock1()
    {
        $mock = \Mockery::mock(EntityClassReflection::class);
        $mock->shouldReceive('getParentClasses')->andReturn(['N86io\Rest\Examples\Example1']);
        $mock->shouldReceive('getProperties')->andReturn([
            'fakeId' => ['type' => 'int'],
            'string' => ['type' => 'string'],
            'array' => ['type' => 'array']
        ]);

        return $mock;
    }

    /**
     * build mock for reflection of Entity2
     */
    protected function createEntityClassReflectionMock2()
    {
        $mock = \Mockery::mock(EntityClassReflection::class);
        $mock->shouldReceive('getParentClasses')->andReturn([]);
        $mock->shouldReceive('getProperties')->andReturn([
            'fakeId' => ['type' => 'int'],
            'string' => ['type' => 'string']
        ]);

        return $mock;
    }

    protected function createPropertyInfoUtilityMock()
    {
        $methodName = 'convertPropertyName';
        $mock = \Mockery::mock(PropertyInfoUtility::class);
        $mock->shouldReceive($methodName)->with('fakeId')->andReturn('fake_id');
        $mock->shouldReceive($methodName)->with('string')->andReturn('string');
        $mock->shouldReceive($methodName)->with('array')->andReturn('array');
    }

    protected function createEntityInfoConfLoaderMock1()
    {
        $mock = \Mockery::mock(EntityInfoConfLoader::class);
        $mock->shouldReceive('loadSingle')->with(
            'Entity1',
            ['N86io\\Rest\\Examples\\Example1']
        )->andReturn([
            'table' => 'fake_table',
            'enableFields' => ['disabled' => 'disableFieldName'],
            'properties' => [
                'fakeId' => ['resourcePropertyName' => 'uid', 'resourceId' => true]
            ]
        ]);
        $mock->shouldReceive('loadSingle')->with('Entity2', [])->andReturn([
            'properties' => [
                'string' => ['ordering' => true]
            ]
        ]);

        return $mock;
    }

    protected function createEntityInfoConfLoaderMock2()
    {
        $mock = \Mockery::mock(EntityInfoConfLoader::class);
        $mock->shouldReceive('loadSingle')->with(
            'Entity1',
            ['N86io\\Rest\\Examples\\Example1']
        )->andReturn([
            'table' => 'fake_table',
            'enableFields' => ['disabled' => 'disableFieldName']
        ]);

        return $mock;
    }
}
