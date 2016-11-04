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

namespace N86io\Rest\Tests\Http\Routing;

use N86io\Rest\Http\Routing\Routing;
use N86io\Rest\Http\Routing\RoutingParameterInterface;
use N86io\Rest\Service\Configuration;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\UriInterface;

/**
 * Class RoutingAndFactoryTest
 * @package N86io\Rest\Tests\Service
 */
class RoutingTest extends UnitTestCase
{
    public function test()
    {
        $confMock = \Mockery::mock(Configuration::class);
        $confMock->shouldReceive('getApiBaseUrl')->andReturn('http://example.com/api');

        $routing = new Routing;

        $routing->addParameter($this->createRoutingParamMock(true, '[\w\d]+', 1, 'version'));
        $routing->addParameter($this->createRoutingParamMock(false, '(api1|api2)', 2, 'apiIdentifier'));
        $routing->addParameter($this->createRoutingParamMock(true, '.+', 1, 'resourceId'));

        $this->inject($routing, 'configuration', $confMock);

        $uri = $this->createUriMock('/api/3/api1/res1,res2');
        $expected = [
            'version' => '3',
            'apiIdentifier' => 'api1',
            'resourceId' => 'res1,res2'
        ];
        $this->assertEquals($expected, $routing->getRoute($uri));

        $uri = $this->createUriMock('/api/api1/res1,res2');
        $expected = [
            'apiIdentifier' => 'api1',
            'resourceId' => 'res1,res2'
        ];
        $this->assertEquals($expected, $routing->getRoute($uri));

        $uri = $this->createUriMock('/api/3/api1');
        $expected = [
            'version' => '3',
            'apiIdentifier' => 'api1'
        ];
        $this->assertEquals($expected, $routing->getRoute($uri));

        $uri = $this->createUriMock('/api/api1');
        $expected = [
            'apiIdentifier' => 'api1'
        ];
        $this->assertEquals($expected, $routing->getRoute($uri));

        $uri = $this->createUriMock('/api/api3');
        $this->assertEquals([], $routing->getRoute($uri));

        $uri = $this->createUriMock('/api/api123');
        $this->assertEquals([], $routing->getRoute($uri));
    }

    /**
     * @param string $path
     * @return UriInterface
     */
    protected function createUriMock($path)
    {
        $mock = \Mockery::mock(UriInterface::class);
        $mock->shouldReceive('getScheme')->andReturn('http');
        $mock->shouldReceive('getHost')->andReturn('example.com');
        $mock->shouldReceive('getPath')->andReturn($path);
        return $mock;
    }

    /**
     * @param bool $isOptional
     * @param string $getExpression
     * @param int $getTakeResult
     * @param string $getName
     * @return RoutingParameterInterface
     */
    protected function createRoutingParamMock($isOptional, $getExpression, $getTakeResult, $getName)
    {
        $routingParamMock = \Mockery::mock(RoutingParameterInterface::class);
        $routingParamMock->shouldReceive('isOptional')->andReturn($isOptional);
        $routingParamMock->shouldReceive('getExpression')->andReturn($getExpression);
        $routingParamMock->shouldReceive('getTakeResult')->andReturn($getTakeResult);
        $routingParamMock->shouldReceive('getName')->andReturn($getName);
        return $routingParamMock;
    }
}
