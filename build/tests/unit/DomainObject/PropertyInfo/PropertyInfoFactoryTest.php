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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo;

use Mockery\MockInterface;
use N86io\Di\Container;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\Relation;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class PropertyInfoFactoryTest extends UnitTestCase
{
    /**
     * @dataProvider buildPropertyInfoDataProvider
     *
     * @param string    $expectedClassName
     * @param string    $name
     * @param string    $type
     * @param Container $containerMock
     */
    public function testBuild(string $expectedClassName, string $name, string $type, $containerMock = null)
    {
        $propertyInfoFactory = new PropertyInfoFactory;
        $this->inject($propertyInfoFactory, 'container', $containerMock);

        $this->assertTrue(
            is_a($propertyInfoFactory->build($name, $type, []), $expectedClassName)
        );
    }

    public function testRegisterPropertyInfoClass()
    {
        $factory = new PropertyInfoFactory;
        $factory->registerPropertyInfoClass(get_class(\Mockery::mock(PropertyInfoInterface::class)));
    }

    public function testBuildEnableField()
    {
        $propInfoFacMock = \Mockery::mock(PropertyInfoFactory::class . '[build]');
        $propInfoFacMock->shouldReceive('build')->withAnyArgs();
        $propInfoFacMock->buildEnableField('deleted', 'resPropName', 'entClassN');
    }

    /**
     * @return array
     */
    public function buildPropertyInfoDataProvider(): array
    {
        $relationClassName = get_class(\Mockery::mock(AbstractEntity::class));
        $containerMock = $this->createContainerMock($relationClassName);

        return [
            [
                Relation::class,
                'somename',
                get_class(\Mockery::mock(AbstractEntity::class)),
                $containerMock
            ],
            [
                Common::class,
                'somename2',
                'string',
                $containerMock
            ]
        ];
    }

    /**
     * @param string $relationClassName
     *
     * @return MockInterface|Container
     */
    protected function createContainerMock($relationClassName)
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(
            Relation::class,
            'somename',
            $relationClassName,
            []
        )->andReturn(\Mockery::mock(Relation::class));
        $mock->shouldReceive('get')->with(
            Common::class,
            'somename2',
            'string',
            []
        )->andReturn(\Mockery::mock(Common::class));

        return $mock;
    }
}
