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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo;

use DI\Container;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\Factory\FactoryInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\Relation;
use N86io\Rest\UnitTestCase;

/**
 * Class PropertyInfoFactoryTest
 * @package N86io\Rest\Tests\Unit\DomainObject\PropertyInfo
 */
class PropertyInfoFactoryTest extends UnitTestCase
{
    /**
     * @dataProvider buildPropertyInfoDataProvider
     * @param $expectedClassName
     * @param $data
     * @param Container $containerMock
     */
    public function testBuild($expectedClassName, $data, $containerMock = null)
    {
        $propertyInfoFactory = new PropertyInfoFactory;
        $this->inject($propertyInfoFactory, 'container', $containerMock);

        $this->assertTrue(
            is_a($propertyInfoFactory->buildPropertyInfo($data['name'], $data['attributes']), $expectedClassName)
        );
    }

    public function testRegister()
    {
        $factory = new PropertyInfoFactory;
        $factory->registerPropertyInfoFactory(\Mockery::mock(FactoryInterface::class));
    }

    public function testRegisterException()
    {
        $factory = new PropertyInfoFactory;
        $this->setExpectedException(\InvalidArgumentException::class);
        $factory->registerPropertyInfoFactory(PropertyInfoFactoryTest::class);
    }

    /**
     * @return array
     */
    public function buildPropertyInfoDataProvider()
    {
        return [
            [
                Relation::class,
                [
                    'name' => 'somename',
                    'attributes' => [
                        'type' => 'Entity1'
                    ]
                ],
                $this->createContainerMock(\N86io\Rest\DomainObject\PropertyInfo\Factory\Relation::class)
            ],
            [
                Common::class,
                [
                    'name' => 'somename2',
                    'attributes' => [
                        'type' => 'string'
                    ]
                ],
                $this->createContainerMock(FactoryInterface::class)
            ]
        ];
    }

    /**
     * @param string $factoryClass
     * @return Container
     */
    protected function createContainerMock($factoryClass)
    {
        $factoryMock = \Mockery::mock($factoryClass);
        $factoryMock->shouldReceive('check')->withAnyArgs()->andReturn(true);
        $factoryMock->shouldReceive('build')->withAnyArgs()->andReturn(\Mockery::mock(Relation::class));

        $wrongFactoryMock = \Mockery::mock(FactoryInterface::class);
        $wrongFactoryMock->shouldReceive('check')->withAnyArgs()->andReturn(false);

        $containerMock = \Mockery::mock(Container::class);
        $containerMock->shouldReceive('get')->with($factoryClass)->andReturn($factoryMock);
        $containerMock->shouldReceive('get')->withAnyArgs()->andReturn($wrongFactoryMock);
        $containerMock->shouldReceive('make')->with(Common::class, [
            'name' => 'somename2',
            'attributes' => [
                'type' => 'string'
            ]
        ])->andReturn(\Mockery::mock(Common::class));

        return $containerMock;
    }
}
