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

use Mockery\MockInterface;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\Relation;
use N86io\Rest\Object\Container;
use N86io\Rest\UnitTestCase;

/**
 * Class PropertyInfoFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
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
            is_a($propertyInfoFactory->build($data['name'], $data['attributes']), $expectedClassName)
        );
    }

    public function testRegisterPropertyInfoClass()
    {
        $factory = new PropertyInfoFactory;
        $factory->registerPropertyInfoClass(\Mockery::mock(PropertyInfoInterface::class));
    }

    public function testBuildEnableField()
    {
        /** @var MockInterface|PropertyInfoFactory $propInfoFacMock */
        $propInfoFacMock = \Mockery::mock(PropertyInfoFactory::class . '[build]')
            ->shouldReceive('build')->withAnyArgs()->getMock();
        $propInfoFacMock->buildEnableField('deleted', 'resPropName', 'entClassN');
    }

    /**
     * @return array
     */
    public function buildPropertyInfoDataProvider()
    {
        $relationClassName = get_class(\Mockery::mock(AbstractEntity::class));
        $containerMock = $this->createContainerMock($relationClassName);
        return [
            [
                Relation::class,
                [
                    'name' => 'somename',
                    'attributes' => [
                        'type' => get_class(\Mockery::mock(AbstractEntity::class))
                    ]
                ],
                $containerMock
            ],
            [
                Common::class,
                [
                    'name' => 'somename2',
                    'attributes' => [
                        'type' => 'string'
                    ]
                ],
                $containerMock
            ]
        ];
    }

    /**
     * @param string $relationClassName
     * @return MockInterface|Container
     */
    protected function createContainerMock($relationClassName)
    {
        return \Mockery::mock(Container::class)
            ->shouldReceive('get')->with(
                Relation::class,
                [
                    'somename',
                    [
                        'type' => $relationClassName
                    ]
                ]
            )->andReturn(\Mockery::mock(Relation::class))->getMock()
            ->shouldReceive('get')->with(
                Common::class,
                [
                    'somename2',
                    [
                        'type' => 'string'
                    ]
                ]
            )->andReturn(\Mockery::mock(Common::class))->getMock();
    }
}
