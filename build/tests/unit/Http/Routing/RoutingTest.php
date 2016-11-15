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

namespace N86io\Rest\Tests\Unit\Http\Routing;

use Mockery\MockInterface;
use N86io\Rest\Http\Routing\Routing;
use N86io\Rest\Http\Routing\RoutingParameterInterface;
use N86io\Rest\Service\Configuration;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\UriInterface;

/**
 * Class RoutingAndFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class RoutingTest extends UnitTestCase
{
    public function test()
    {
        /** @var MockInterface|Configuration $confMock */
        $confMock = \Mockery::mock(Configuration::class)
            ->shouldReceive('getApiBaseUrl')->andReturn('http://example.com/api')->getMock();

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
     * @return MockInterface|UriInterface
     */
    protected function createUriMock($path)
    {
        return \Mockery::mock(UriInterface::class)
            ->shouldReceive('getScheme')->andReturn('http')->getMock()
            ->shouldReceive('getHost')->andReturn('example.com')->getMock()
            ->shouldReceive('getPath')->andReturn($path)->getMock();
    }

    /**
     * @param bool $isOptional
     * @param string $getExpression
     * @param int $getTakeResult
     * @param string $getName
     * @return MockInterface|RoutingParameterInterface
     */
    protected function createRoutingParamMock($isOptional, $getExpression, $getTakeResult, $getName)
    {
        return \Mockery::mock(RoutingParameterInterface::class)
            ->shouldReceive('isOptional')->andReturn($isOptional)->getMock()
            ->shouldReceive('getExpression')->andReturn($getExpression)->getMock()
            ->shouldReceive('getTakeResult')->andReturn($getTakeResult)->getMock()
            ->shouldReceive('getName')->andReturn($getName)->getMock();
    }
}
