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

namespace N86io\Rest\Tests\Http\Utility;

use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\Http\Utility\QueryUtility;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class QueryUtilityTest
 * @package N86io\Rest\Tests\Http\Utility
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
        parent::setUp();
        $entityInfoStorage = ObjectContainer::get(EntityInfoStorage::class);
        $this->entityInfo = $entityInfoStorage->get(FakeEntity1::class);
        $this->queryUtility = ObjectContainer::get(QueryUtility::class);
    }

    public function test1()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $uri->shouldReceive('getQuery')->andReturn('integer.gt=123&sort=string.asc&limit=10&page=2&level=5');
        $serverRequest = \Mockery::mock(ServerRequestInterface::class);
        $serverRequest->shouldReceive('getUri')->andReturn($uri);

        $queryParams = $this->queryUtility->resolveQueryParams($serverRequest, $this->entityInfo);

        $this->assertEquals('string', $queryParams['ordering'][0]->getPropertyInfo()->getName());
        $this->assertEquals(10, $queryParams['limit']);
        $this->assertEquals(2, $queryParams['page']);
        $this->assertEquals(5, $queryParams['outputLevel']);
        $this->assertEquals('integer', $queryParams['constraints']->getConstraints()[0]->getLeftOperand()->getName());
    }

    public function test2()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $uri->shouldReceive('getQuery')->andReturn('invalidPropertyName.gt=10&sort=invalidPropertyName.asc');
        $serverRequest = \Mockery::mock(ServerRequestInterface::class);
        $serverRequest->shouldReceive('getUri')->andReturn($uri);

        $queryParams = $this->queryUtility->resolveQueryParams($serverRequest, $this->entityInfo);

        $this->assertEmpty($queryParams['ordering']);
        $this->assertFalse(array_key_exists('constraints', $queryParams));
    }

    public function test3()
    {
        $uri = \Mockery::mock(UriInterface::class);
        $uri->shouldReceive('getQuery')->andReturn('string.gt=10&sort=string.desc');
        $serverRequest = \Mockery::mock(ServerRequestInterface::class);
        $serverRequest->shouldReceive('getUri')->andReturn($uri);

        $queryParams = $this->queryUtility->resolveQueryParams($serverRequest, $this->entityInfo);

        $this->assertEquals('string', $queryParams['ordering'][0]->getPropertyInfo()->getName());
        $this->assertFalse(array_key_exists('constraints', $queryParams));
    }
}
