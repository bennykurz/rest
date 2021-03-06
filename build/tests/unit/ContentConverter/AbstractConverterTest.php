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

namespace N86io\Rest\Tests\Unit\ContentConverter;

use N86io\Rest\Authorization\AuthorizationInterface;
use N86io\Rest\ContentConverter\ParsableInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
abstract class AbstractConverterTest extends UnitTestCase
{
    protected $entityInfoMockClassName = '';

    /**
     * @return array
     */
    protected function createList()
    {
        return [
            'key1' => 'value1',
            'key2' => [
                'key2_1' => 'value2'
            ],
            'key3' => \Mockery::mock(EntityInterface::class)
                ->shouldReceive('getNameTwo')->andReturn('_name_two_')->getMock()
                ->shouldReceive('getProperty')->with('nameOne')->andReturn('_name_one_')->getMock(),
            'key4' => \DateTime::createFromFormat('Y-m-d H:i:s e', '2016-11-15 10:42:26 UTC'),
            'key5' => \Mockery::mock(ParsableInterface::class)
                ->shouldReceive('getParsedValue')->andReturn('valueOf5')->getMock()
        ];
    }

    /**
     * @return \Mockery\MockInterface|EntityInfoStorage
     */
    protected function createEntityInfoStorageMock()
    {
        $entityInfoMock = \Mockery::mock(EntityInfoInterface::class);
        $this->entityInfoMockClassName = get_class($entityInfoMock);
        $entityInfoMock->shouldReceive('getVisiblePropertiesOrdered')->withAnyArgs()->andReturn([
            \Mockery::mock(PropertyInfoInterface::class)
                ->shouldReceive('getGetter')->andReturn('')->getMock()
                ->shouldReceive('getName')->andReturn('nameOne')->getMock()
                ->shouldReceive('getEntityInfo')->andReturn($entityInfoMock)->getMock(),
            \Mockery::mock(PropertyInfoInterface::class)
                ->shouldReceive('getGetter')->andReturn('getNameTwo')->getMock()
                ->shouldReceive('getName')->andReturn('nameTwo')->getMock()
                ->shouldReceive('getEntityInfo')->andReturn($entityInfoMock)->getMock(),
            \Mockery::mock(PropertyInfoInterface::class)
                ->shouldReceive('getGetter')->andReturn('')->getMock()
                ->shouldReceive('getName')->andReturn('nameThree')->getMock()
                ->shouldReceive('getEntityInfo')->andReturn($entityInfoMock)->getMock()
        ]);
        $entityInfoMock->shouldReceive('getClassName')->andReturn($this->entityInfoMockClassName);

        $mock = \Mockery::mock(EntityInfoStorage::class);
        $mock->shouldReceive('get')->withAnyArgs()->andReturn($entityInfoMock);

        return $mock;
    }

    /**
     * @return \Mockery\MockInterface|AuthorizationInterface
     */
    protected function createAuthorizationMock()
    {
        return \Mockery::mock(AuthorizationInterface::class)
            ->shouldReceive('hasPropertyReadAuthorization')->with(
                $this->entityInfoMockClassName,
                'nameOne'
            )->andReturn(true)->getMock()
            ->shouldReceive('hasPropertyReadAuthorization')->with(
                $this->entityInfoMockClassName,
                'nameTwo'
            )->andReturn(true)->getMock()
            ->shouldReceive('hasPropertyReadAuthorization')->with(
                $this->entityInfoMockClassName,
                'nameThree'
            )->andReturn(false)->getMock();
    }
}
