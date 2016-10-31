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

namespace N86io\Rest\Tests\Http;

use GuzzleHttp\Psr7\ServerRequest;
use N86io\Rest\Exception\InvalidRequestException;
use N86io\Rest\Http\RequestFactory;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Service\Configuration;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\UnitTestCase;

/**
 * Class RequestFactoryTest
 * @package N86io\Rest\Tests\Http
 */
class RequestFactoryTest extends UnitTestCase
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function setUp()
    {
        parent::setUp();
        Configuration::setApiBaseUrl('http://example.com/api');
        Configuration::registerApiModel('api1', FakeEntity1::class, 1);
        $this->requestFactory = ObjectContainer::get(RequestFactory::class);
    }

    public function test()
    {
        $serverRequest = new ServerRequest('GET', 'http://example.com/api/api1');
        $request = $this->requestFactory->fromServerRequest($serverRequest);
        $this->assertEquals('api1', $request->getApiIdentifier());
        $this->assertEquals('N86io\Rest\Tests\DomainObject\FakeEntity1', $request->getModelClassName());
        $this->assertEquals('N86io\Rest\ControllerInterface', $request->getControllerClassName());

        $serverRequest = new ServerRequest('GET', 'http://example.com/api/1/api1/res1,res2?integer.gt=123' .
            '&sort=string.asc&limit=10&page=2&level=5');
        $request = $this->requestFactory->fromServerRequest($serverRequest);
        $this->assertEquals('1', $request->getVersion());
        $this->assertEquals('api1', $request->getApiIdentifier());
        $this->assertEquals('N86io\Rest\Tests\DomainObject\FakeEntity1', $request->getModelClassName());
        $this->assertEquals('N86io\Rest\ControllerInterface', $request->getControllerClassName());
    }

    public function testCurrentlyNotUsedMethods()
    {
        $serverRequest = new ServerRequest('POST', 'http://example.com/api/api1');
        $this->assertEquals(
            RequestInterface::REQUEST_MODE_CREATE,
            $this->requestFactory->fromServerRequest($serverRequest)->getMode()
        );
        $serverRequest = new ServerRequest('PATCH', 'http://example.com/api/api1');
        $this->assertEquals(
            RequestInterface::REQUEST_MODE_PATCH,
            $this->requestFactory->fromServerRequest($serverRequest)->getMode()
        );
        $serverRequest = new ServerRequest('PUT', 'http://example.com/api/api1');
        $this->assertEquals(
            RequestInterface::REQUEST_MODE_UPDATE,
            $this->requestFactory->fromServerRequest($serverRequest)->getMode()
        );
        $serverRequest = new ServerRequest('DELETE', 'http://example.com/api/api1');
        $this->assertEquals(
            RequestInterface::REQUEST_MODE_DELETE,
            $this->requestFactory->fromServerRequest($serverRequest)->getMode()
        );
    }

    public function testWrongApiIdentifier()
    {
        $this->setExpectedException(InvalidRequestException::class);
        $serverRequest = new ServerRequest('GET', 'http://example.com/api/api12345');
        $this->requestFactory->fromServerRequest($serverRequest);
    }

    public function testUnavailableVersion()
    {
        $this->setExpectedException(InvalidRequestException::class);
        $serverRequest = new ServerRequest('GET', 'http://example.com/api/200/api1');
        $this->requestFactory->fromServerRequest($serverRequest);
    }
}
