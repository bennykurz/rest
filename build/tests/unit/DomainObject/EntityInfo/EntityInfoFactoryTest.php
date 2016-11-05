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

use DI\Container;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoConfLoader;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;
use N86io\Rest\Reflection\EntityClassReflection;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityInfoFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoFactoryTest extends UnitTestCase
{
    public function test()
    {
        $factory = new EntityInfoFactory;
        $this->inject($factory, 'entityInfoConfLoader', $this->createEntityInfoConfLoaderMock1());
        $this->inject($factory, 'propertyInfoUtility', $this->createPropertyInfoUtilityMock());
        $this->inject($factory, 'propertyInfoFactory', $this->createPropertyInfoFactoryMock());
        $this->inject($factory, 'container', $this->createContainerMock());

        $factory->buildEntityInfoFromClassName('Entity2');
        $factory->buildEntityInfoFromClassName('Entity4');

        $this->inject($factory, 'entityInfoConfLoader', $this->createEntityInfoConfLoaderMock2());
        $factory->buildEntityInfoFromClassName('Entity2');
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @return PropertyInfoFactory
     */
    protected function createPropertyInfoFactoryMock()
    {
        $mock = \Mockery::mock(PropertyInfoFactory::class);
        $mock->shouldReceive('buildPropertyInfo')->withAnyArgs()
            ->andReturn(\Mockery::mock(PropertyInfoInterface::class));

        return $mock;
    }

    /**
     * @return Container
     */
    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('make')
            ->with(EntityClassReflection::class, ['className' => 'Entity2'])
            ->andReturn($this->createEntityClassReflectionMock1());

        $mock->shouldReceive('make')
            ->with(EntityClassReflection::class, ['className' => 'Entity4'])
            ->andReturn($this->createEntityClassReflectionMock2());

        $mock->shouldReceive('make')
            ->with(EntityInfo::class, [
                'attributes' => [
                    'className' => 'Entity2',
                    'table' => 'fake_table'
                ]
            ])
            ->andReturn($this->createEntityInfoMock1());

        $mock->shouldReceive('make')
            ->with(
                EntityInfo::class,
                ['attributes' => ['className' => 'Entity4']]
            )
            ->andReturn($this->createEntityInfoMock2());

        return $mock;
    }

    /**
     * build mock for EntityInfo for class Entity2
     *
     * @return EntityInfo
     */
    protected function createEntityInfoMock1()
    {
        $mock = \Mockery::mock(EntityInfo::class);
        $mock->shouldReceive('addPropertyInfo')->withAnyArgs();
        $mock->shouldReceive('hasUidPropertyInfo')->andReturn(true);

        return $mock;
    }

    /**
     * build mock for EntityInfo for class Entity4
     *
     * @return EntityInfo
     */
    protected function createEntityInfoMock2()
    {
        $mock = \Mockery::mock(EntityInfo::class);
        $mock->shouldReceive('addPropertyInfo')->withAnyArgs();
        $mock->shouldReceive('hasUidPropertyInfo')->andReturn(false);
        $mock->shouldReceive('hasResourceId')->andReturn(false);

        return $mock;
    }

    /**
     * build mock for reflection of Entity2
     *
     * @return EntityClassReflection
     */
    protected function createEntityClassReflectionMock1()
    {
        $mock = \Mockery::mock(EntityClassReflection::class);
        $mock->shouldReceive('getParentClasses')
            ->andReturn(['N86io\Rest\Examples\Example1']);
        $mock->shouldReceive('getProperties')
            ->andReturn([
                'fakeId' => ['type' => 'int'],
                'string' => ['type' => 'string'],
                'array' => ['type' => 'array']
            ]);

        return $mock;
    }

    /**
     * build mock for reflection of Entity4
     *
     * @return EntityClassReflection
     */
    protected function createEntityClassReflectionMock2()
    {
        $mock = \Mockery::mock(EntityClassReflection::class);
        $mock->shouldReceive('getParentClasses')
            ->andReturn([]);
        $mock->shouldReceive('getProperties')
            ->andReturn([
                'fakeId' => ['type' => 'int'],
                'string' => ['type' => 'string']
            ]);

        return $mock;
    }

    /**
     * @return PropertyInfoUtility
     */
    protected function createPropertyInfoUtilityMock()
    {
        $methodName = 'convertPropertyName';
        $mock = \Mockery::mock(PropertyInfoUtility::class);
        $mock->shouldReceive($methodName)->with('fakeId')->andReturn('fake_id')
            ->getMock()->shouldReceive($methodName)->with('string')->andReturn('string')
            ->getMock()->shouldReceive($methodName)->with('array')->andReturn('array');

        return $mock;
    }

    /**
     * @return EntityInfoConfLoader
     */
    protected function createEntityInfoConfLoaderMock1()
    {
        $mock = \Mockery::mock(EntityInfoConfLoader::class);
        $mock->shouldReceive('loadSingle')
            ->with(
                'Entity2',
                ['N86io\\Rest\\Examples\\Example1']
            )
            ->andReturn([
                'table' => 'fake_table',
                'properties' => [
                    'fakeId' => ['resourcePropertyName' => 'uid', 'resourceId' => true]
                ]
            ]);

        $mock->shouldReceive('loadSingle')
            ->with('Entity4', [])
            ->andReturn([
                'properties' => [
                    'string' => ['ordering' => true]
                ]
            ]);

        return $mock;
    }

    /**
     * @return EntityInfoConfLoader
     */
    protected function createEntityInfoConfLoaderMock2()
    {
        $mock = \Mockery::mock(EntityInfoConfLoader::class);
        $mock->shouldReceive('loadSingle')
            ->with(
                'Entity2',
                ['N86io\\Rest\\Examples\\Example1']
            )
            ->andReturn(['table' => 'fake_table']);

        return $mock;
    }
}