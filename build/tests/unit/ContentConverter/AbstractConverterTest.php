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

namespace N86io\Rest\Tests\Unit\ContentConverter;

use Mockery\MockInterface;
use N86io\Rest\ContentConverter\ParsableInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class AbstractConverterTest
 *
 * @author Viktor Firus <v@n86.io>
 */
abstract class AbstractConverterTest extends UnitTestCase
{
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
        /** @var MockInterface|EntityInfoInterface $entityInfoMock */
        $entityInfoMock = \Mockery::mock(EntityInfoInterface::class)
            ->shouldReceive('getVisiblePropertiesOrdered')->withAnyArgs()->andReturn([
                \Mockery::mock(PropertyInfoInterface::class)
                    ->shouldReceive('getGetter')->andReturn('')->getMock()
                    ->shouldReceive('getName')->andReturn('nameOne')->getMock(),
                \Mockery::mock(PropertyInfoInterface::class)
                    ->shouldReceive('getGetter')->andReturn('getNameTwo')->getMock()
                    ->shouldReceive('getName')->andReturn('nameTwo')->getMock()
            ])->getMock();

        return \Mockery::mock(EntityInfoStorage::class)
            ->shouldReceive('get')->withAnyArgs()->andReturn($entityInfoMock)->getMock();
    }
}
