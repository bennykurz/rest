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

namespace N86io\Rest\Tests\Unit\Http\Utility;

use N86io\Di\Container;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\Http\Utility\QueryUtility;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Ordering\OrderingFactory;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\UnitTestCase;

/**
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
        $this->assertTrue($queryParams['constraints'][0] instanceof ConstraintInterface);


        $queryParams = $this->queryUtility->resolveQueryParams(
            'sort=string.desc',
            $this->entityInfo
        );
        $this->assertTrue($queryParams['ordering'] instanceof OrderingInterface);


        $queryParams = $this->queryUtility->resolveQueryParams(
            'invalidPropertyName.gt=10&sort=invalidPropertyName.asc',
            $this->entityInfo
        );
        $this->assertArrayNotHasKey('ordering', $queryParams);
        $this->assertArrayNotHasKey('constraints', $queryParams);

        $queryParams = $this->queryUtility->resolveQueryParams(
            'string.gt=10&sort=integer.desc',
            $this->entityInfo
        );
        $this->assertArrayNotHasKey('ordering', $queryParams);
        $this->assertArrayNotHasKey('constraints', $queryParams);
    }

    protected function createContainerMock()
    {
        $orderingFactory = \Mockery::mock(OrderingFactory::class);
        $orderingFactory->shouldReceive('descending')->withAnyArgs()
            ->andReturn(\Mockery::mock(OrderingInterface::class));
        $orderingFactory->shouldReceive('ascending')->withAnyArgs()
            ->andReturn(\Mockery::mock(OrderingInterface::class));

        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(OrderingFactory::class)->andReturn($orderingFactory);

        return $mock;
    }

    protected function createConstraintFactoryMock()
    {
        $mock = \Mockery::mock(ConstraintFactory::class);
        $mock->shouldReceive('createComparisonFromStringDetection')->withAnyArgs()
            ->andReturn(\Mockery::mock(ConstraintInterface::class));
        $mock->shouldReceive('logicalAnd')->withAnyArgs()->andReturn(\Mockery::mock(ConstraintInterface::class));

        return $mock;
    }

    protected function createEntityInfoMock()
    {
        $mock = \Mockery::mock(EntityInfo::class);
        $mock->shouldReceive('hasPropertyInfo')->with('integer')->andReturn(true);
        $mock->shouldReceive('hasPropertyInfo')->with('string')->andReturn(true);
        $mock->shouldReceive('hasPropertyInfo')->with('invalidPropertyName')->andReturn(false);
        $mock->shouldReceive('getPropertyInfo')->with('integer')->andReturn(
            \Mockery::mock(Common::class)
                ->shouldReceive('isConstraint')->andReturn(true)
                ->getMock()
                ->shouldReceive('isOrdering')->andReturn(false)
                ->getMock()
        );
        $mock->shouldReceive('getPropertyInfo')->with('string')->andReturn(
            \Mockery::mock(Common::class)
                ->shouldReceive('isConstraint')->andReturn(false)
                ->getMock()
                ->shouldReceive('isOrdering')->andReturn(true)
                ->getMock()
        );

        return $mock;
    }
}
