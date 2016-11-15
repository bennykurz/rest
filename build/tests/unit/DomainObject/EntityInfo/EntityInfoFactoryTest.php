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

use Mockery\MockInterface;
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

    /**
     * @return MockInterface|PropertyInfoFactory
     */
    protected function createPropertyInfoFactoryMock()
    {
        return \Mockery::mock(PropertyInfoFactory::class)
            ->shouldReceive('build')->withAnyArgs()->andReturn(\Mockery::mock(PropertyInfoInterface::class))->getMock()
            ->shouldReceive('buildEnableField')->withAnyArgs()->andReturn(
                \Mockery::mock(PropertyInfoInterface::class)
            )->getMock();
    }

    /**
     * @return MockInterface|Container
     */
    protected function createContainerMock()
    {
        return \Mockery::mock(Container::class)
            ->shouldReceive('get')->with(EntityClassReflection::class, ['Entity1'])
            ->andReturn($this->createEntityClassReflectionMock1())->getMock()
            ->shouldReceive('get')->with(EntityClassReflection::class, ['Entity2'])
            ->andReturn($this->createEntityClassReflectionMock2())->getMock()
            ->shouldReceive('get')->with(EntityInfo::class, [
                [
                    'className' => 'Entity1',
                    'table' => 'fake_table',
                    'enableFields' => ['disabled' => 'disableFieldName']
                ]
            ])->andReturn($this->createEntityInfoMock1())->getMock()
            ->shouldReceive('get')->with(
                EntityInfo::class,
                [['className' => 'Entity2']]
            )->andReturn($this->createEntityInfoMock2())->getMock();
    }

    /**
     * build mock for EntityInfo for class Entity1
     *
     * @return MockInterface|EntityInfo
     */
    protected function createEntityInfoMock1()
    {
        return \Mockery::mock(EntityInfo::class)
            ->shouldReceive('addPropertyInfo')->withAnyArgs()->getMock()
            ->shouldReceive('hasUidPropertyInfo')->andReturn(true)->getMock()
            ->shouldReceive('getClassName')->withAnyArgs()->andReturn('ClassName')->getMock();
    }

    /**
     * build mock for EntityInfo for class Entity2
     *
     * @return MockInterface|EntityInfo
     */
    protected function createEntityInfoMock2()
    {
        return \Mockery::mock(EntityInfo::class)
            ->shouldReceive('addPropertyInfo')->withAnyArgs()->getMock()
            ->shouldReceive('hasUidPropertyInfo')->andReturn(false)->getMock()
            ->shouldReceive('hasResourceId')->andReturn(false)->getMock()
            ->shouldReceive('getClassName')->withAnyArgs()->andReturn('ClassName')->getMock();
    }

    /**
     * build mock for reflection of Entity1
     *
     * @return MockInterface|EntityClassReflection
     */
    protected function createEntityClassReflectionMock1()
    {
        return \Mockery::mock(EntityClassReflection::class)
            ->shouldReceive('getParentClasses')->andReturn(['N86io\Rest\Examples\Example1'])->getMock()
            ->shouldReceive('getProperties')->andReturn([
                'fakeId' => ['type' => 'int'],
                'string' => ['type' => 'string'],
                'array' => ['type' => 'array']
            ])->getMock();
    }

    /**
     * build mock for reflection of Entity2
     *
     * @return MockInterface|EntityClassReflection
     */
    protected function createEntityClassReflectionMock2()
    {
        return \Mockery::mock(EntityClassReflection::class)
            ->shouldReceive('getParentClasses')->andReturn([])->getMock()
            ->shouldReceive('getProperties')->andReturn([
                'fakeId' => ['type' => 'int'],
                'string' => ['type' => 'string']
            ])->getMock();
    }

    /**
     * @return MockInterface|PropertyInfoUtility
     */
    protected function createPropertyInfoUtilityMock()
    {
        $methodName = 'convertPropertyName';
        return \Mockery::mock(PropertyInfoUtility::class)
            ->shouldReceive($methodName)->with('fakeId')->andReturn('fake_id')->getMock()
            ->shouldReceive($methodName)->with('string')->andReturn('string')->getMock()
            ->shouldReceive($methodName)->with('array')->andReturn('array')->getMock();
    }

    /**
     * @return MockInterface|EntityInfoConfLoader
     */
    protected function createEntityInfoConfLoaderMock1()
    {
        return \Mockery::mock(EntityInfoConfLoader::class)
            ->shouldReceive('loadSingle')->with(
                'Entity1',
                ['N86io\\Rest\\Examples\\Example1']
            )->andReturn([
                'table' => 'fake_table',
                'enableFields' => ['disabled' => 'disableFieldName'],
                'properties' => [
                    'fakeId' => ['resourcePropertyName' => 'uid', 'resourceId' => true]
                ]
            ])->getMock()
            ->shouldReceive('loadSingle')->with('Entity2', [])->andReturn([
                'properties' => [
                    'string' => ['ordering' => true]
                ]
            ])->getMock();
    }

    /**
     * @return MockInterface|EntityInfoConfLoader
     */
    protected function createEntityInfoConfLoaderMock2()
    {
        return \Mockery::mock(EntityInfoConfLoader::class)
            ->shouldReceive('loadSingle')->with(
                'Entity1',
                ['N86io\\Rest\\Examples\\Example1']
            )->andReturn([
                'table' => 'fake_table',
                'enableFields' => ['disabled' => 'disableFieldName']
            ])->getMock();
    }
}
