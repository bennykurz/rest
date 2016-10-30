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

use GuzzleHttp\Psr7\ServerRequest;
use N86io\Rest\Http\Routing\RoutingFactory;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Service\Configuration;

/**
 * Class RoutingAndFactoryTest
 * @package N86io\Rest\Tests\Service
 */
class RoutingAndFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        Configuration::setApiBaseUrl('http://example.com/api');

        /** @var RoutingFactory $routingFactory */
        $routingFactory = ObjectContainer::get(RoutingFactory::class);
        $routing = $routingFactory->build(['api1', 'api2']);

        $serverRequest = new ServerRequest('GET', 'http://example.com/api/3/api1/res1,res2');
        $expected = [
            'version' => '3',
            'apiIdentifier' => 'api1',
            'resourceId' => 'res1,res2'
        ];
        $this->assertEquals($expected, $routing->getRoute($serverRequest));

        $serverRequest = new ServerRequest('GET', 'http://example.com/api/api1/res1,res2');
        $expected = [
            'apiIdentifier' => 'api1',
            'resourceId' => 'res1,res2'
        ];
        $this->assertEquals($expected, $routing->getRoute($serverRequest));

        $serverRequest = new ServerRequest('GET', 'http://example.com/api/3/api1');
        $expected = [
            'version' => '3',
            'apiIdentifier' => 'api1'
        ];
        $this->assertEquals($expected, $routing->getRoute($serverRequest));

        $serverRequest = new ServerRequest('GET', 'http://example.com/api/api1');
        $expected = [
            'apiIdentifier' => 'api1'
        ];
        $this->assertEquals($expected, $routing->getRoute($serverRequest));

        $serverRequest = new ServerRequest('GET', 'http://example.com/api/api3');
        $this->assertEquals([], $routing->getRoute($serverRequest));
    }
}
