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

use DI\Container;
use N86io\Rest\Http\Routing\Routing;
use N86io\Rest\Http\Routing\RoutingFactory;
use N86io\Rest\Http\Routing\RoutingParameterInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class RoutingFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class RoutingFactoryTest extends UnitTestCase
{
    public function test()
    {
        $routingFactory = new RoutingFactory;
        $this->inject($routingFactory, 'container', $this->createContainerMock());
        $routing = $routingFactory->build(['api1', 'api2']);
        $this->assertTrue($routing instanceof Routing);
    }

    /**
     * @return Container
     */
    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(Routing::class)->andReturn(
            \Mockery::mock(Routing::class)->shouldReceive('addParameter')->withAnyArgs()->getMock()
        );

        $mock->shouldReceive('make')->with(
            RoutingParameterInterface::class,
            [
                'name' => 'version',
                'expression' => '[\w\d]+',
                'optional' => true
            ]
        )->andReturn(\Mockery::mock(RoutingParameterInterface::class));

        $mock->shouldReceive('make')->with(
            RoutingParameterInterface::class,
            [
                'name' => 'apiIdentifier',
                'expression' => '(api1|api2)',
                'optional' => false,
                'takeResult' => 2
            ]
        )->andReturn(\Mockery::mock(RoutingParameterInterface::class));

        $mock->shouldReceive('make')->with(
            RoutingParameterInterface::class,
            [
                'name' => 'resourceId',
                'expression' => '.+',
                'optional' => true
            ]
        )->andReturn(\Mockery::mock(RoutingParameterInterface::class));

        return $mock;
    }
}
