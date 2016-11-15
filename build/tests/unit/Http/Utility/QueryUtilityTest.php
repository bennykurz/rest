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

namespace N86io\Rest\Tests\Unit\Http\Utility;

use Mockery\MockInterface;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\Http\Utility\QueryUtility;
use N86io\Rest\Object\Container;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Ordering\OrderingFactory;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class QueryUtilityTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class QueryUtilityTest extends UnitTestCase
{
    /**
     * @var EntityInfo
     */
    protected $entityInfo;

    /**
     * @var QueryUtility
     */
    protected $queryUtility;

    public function setUp()
    {
        $this->entityInfo = $this->createEntityInfoMock();

        $this->queryUtility = new QueryUtility;
        $this->inject($this->queryUtility, 'container', $this->createContainerMock());
        $this->inject($this->queryUtility, 'constraintFactory', $this->createConstraintFactoryMock());
    }

    public function test()
    {
        $queryParams = $this->queryUtility->resolveQueryParams(
            'integer.gt=123&sort=string.asc&rowCount=10&offset=2&level=5',
            $this->entityInfo
        );
        $this->assertEquals(10, $queryParams['rowCount']);
        $this->assertEquals(2, $queryParams['offset']);
        $this->assertEquals(5, $queryParams['outputLevel']);
        $this->assertTrue($queryParams['ordering'] instanceof OrderingInterface);
        $this->assertTrue($queryParams['constraints'] instanceof ConstraintInterface);


        $queryParams = $this->queryUtility->resolveQueryParams(
            'sort=string.desc',
            $this->entityInfo
        );
        $this->assertTrue($queryParams['ordering'] instanceof OrderingInterface);


        $queryParams = $this->queryUtility->resolveQueryParams(
            'invalidPropertyName.gt=10&sort=invalidPropertyName.asc',
            $this->entityInfo
        );
        $this->assertFalse(array_key_exists('ordering', $queryParams));
        $this->assertFalse(array_key_exists('constraints', $queryParams));


        $queryParams = $this->queryUtility->resolveQueryParams(
            'string.gt=10&sort=integer.desc',
            $this->entityInfo
        );
        $this->assertFalse(array_key_exists('ordering', $queryParams));
        $this->assertFalse(array_key_exists('constraints', $queryParams));
    }

    /**
     * @return MockInterface|Container
     */
    protected function createContainerMock()
    {
        $orderingFactory = \Mockery::mock(OrderingFactory::class)
            ->shouldReceive('descending')->withAnyArgs()->andReturn(\Mockery::mock(OrderingInterface::class))->getMock()
            ->shouldReceive('ascending')->withAnyArgs()->andReturn(\Mockery::mock(OrderingInterface::class))->getMock();

        return \Mockery::mock(Container::class)
            ->shouldReceive('get')->with(OrderingFactory::class)->andReturn($orderingFactory)->getMock();
    }

    /**
     * @return MockInterface|ConstraintFactory
     */
    protected function createConstraintFactoryMock()
    {
        return \Mockery::mock(ConstraintFactory::class)
            ->shouldReceive('createComparisonFromStringDetection')->withAnyArgs()
            ->andReturn(\Mockery::mock(ConstraintInterface::class))->getMock()
            ->shouldReceive('logicalAnd')->withAnyArgs()->andReturn(\Mockery::mock(ConstraintInterface::class))
            ->getMock();
    }

    /**
     * @return MockInterface|AbstractEntity
     */
    protected function createEntityInfoMock()
    {
        return \Mockery::mock(EntityInfo::class)
            ->shouldReceive('hasPropertyInfo')->with('integer')->andReturn(true)->getMock()
            ->shouldReceive('hasPropertyInfo')->with('string')->andReturn(true)->getMock()
            ->shouldReceive('hasPropertyInfo')->with('invalidPropertyName')->andReturn(false)->getMock()
            ->shouldReceive('getPropertyInfo')->with('integer')->andReturn(
                \Mockery::mock(Common::class)
                    ->shouldReceive('isConstraint')->andReturn(true)
                    ->getMock()
                    ->shouldReceive('isOrdering')->andReturn(false)
                    ->getMock()
            )->getMock()
            ->shouldReceive('getPropertyInfo')->with('string')->andReturn(
                \Mockery::mock(Common::class)
                    ->shouldReceive('isConstraint')->andReturn(false)
                    ->getMock()
                    ->shouldReceive('isOrdering')->andReturn(true)
                    ->getMock()
            )->getMock();
    }
}
