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

namespace N86io\Rest\Tests\Unit\Http;

use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\Exception\RequestNotFoundException;
use N86io\Rest\Http\RequestFactory;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\Http\Routing\Routing;
use N86io\Rest\Http\Routing\RoutingFactory;
use N86io\Rest\Http\Utility\QueryUtility;
use N86io\Rest\Object\Container;
use N86io\Rest\Persistence\Constraint\LogicalInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\Service\Configuration;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class RequestFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class RequestFactoryTest extends UnitTestCase
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function setUp()
    {
        $entityInfoStorage = $this->createEntityInfoStorageMock();

        $this->requestFactory = new RequestFactory;
        $this->inject($this->requestFactory, 'container', $this->createContainerMock());
        $this->inject($this->requestFactory, 'configuration', $this->createConfigurationMock());
        $this->inject($this->requestFactory, 'entityInfoStorage', $entityInfoStorage);
        $this->inject(
            $this->requestFactory,
            'queryUtility',
            $this->createQueryUtilityMock($entityInfoStorage->get('Entity1'))
        );
    }

    public function test()
    {
        $mocks = $this->createMocksWithoutQuery('GET');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->assertTrue($this->requestFactory->fromServerRequest($serverRequest) instanceof RequestInterface);

        $mocks = $this->createMocksWithQuery('GET');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->assertTrue($this->requestFactory->fromServerRequest($serverRequest) instanceof RequestInterface);
    }

    public function testCurrentlyNotUsedMethods()
    {
        $mocks = $this->createMocksWithoutQuery('POST');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->assertTrue($this->requestFactory->fromServerRequest($serverRequest) instanceof RequestInterface);

        $mocks = $this->createMocksWithoutQuery('PATCH');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->assertTrue($this->requestFactory->fromServerRequest($serverRequest) instanceof RequestInterface);

        $mocks = $this->createMocksWithoutQuery('PUT');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->assertTrue($this->requestFactory->fromServerRequest($serverRequest) instanceof RequestInterface);


        $mocks = $this->createMocksWithoutQuery('DELETE');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->assertTrue($this->requestFactory->fromServerRequest($serverRequest) instanceof RequestInterface);
    }

    public function testWrongApiIdentifier()
    {
        $mocks = $this->createServerRequestAndRoutingFactoryMocks('GET', []);
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);

        $this->setExpectedException(RequestNotFoundException::class);
        $this->requestFactory->fromServerRequest($serverRequest);
    }

    public function testUnavailableVersion()
    {
        $mocks = $this->createMocksWithQuery('GET');
        $serverRequest = $mocks['serverRequest'];
        $this->inject($this->requestFactory, 'routingFactory', $mocks['routingFactory']);
        $this->inject($this->requestFactory, 'configuration', $this->createConfigurationMock2());

        $this->setExpectedException(RequestNotFoundException::class);
        $this->requestFactory->fromServerRequest($serverRequest);
    }

    /**
     * @return Container
     */
    protected function createContainerMock()
    {
        $requestMock = \Mockery::mock(RequestInterface::class);
        $requestMock->shouldReceive('setVersion')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setApiIdentifier')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setResourceIds')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setOrdering')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setLimit')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setPage')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setOutputLevel')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setModelClassName')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setControllerClassName')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setMode')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setConstraints')->withAnyArgs()->andReturn($requestMock);
        $requestMock->shouldReceive('setRoute')->withAnyArgs()->andReturn($requestMock);

        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(RequestInterface::class)->andReturn($requestMock);
        return $mock;
    }

    /**
     * @param string $method
     * @return array
     */
    protected function createMocksWithoutQuery($method)
    {
        return $this->createServerRequestAndRoutingFactoryMocks($method, ['apiIdentifier' => 'api1']);
    }

    /**
     * @param string $method
     * @return array
     */
    protected function createMocksWithQuery($method)
    {
        return $this->createServerRequestAndRoutingFactoryMocks(
            $method,
            [
                'version' => 1,
                'apiIdentifier' => 'api1',
                'resourceId' => 'res1,res2'
            ],
            'integer.gt=123&sort=string.asc&limit=10&page=2&level=5'
        );
    }

    /**
     * @param string $method
     * @param array $route
     * @param string $query
     * @return array
     */
    protected function createServerRequestAndRoutingFactoryMocks($method, array $route, $query = '')
    {
        $uriMock = \Mockery::mock(UriInterface::class);
        $uriMock->shouldReceive('getQuery')->andReturn($query);

        $serverRequestMock = \Mockery::mock(ServerRequestInterface::class);
        $serverRequestMock->shouldReceive('getUri')->andReturn($uriMock);
        $serverRequestMock->shouldReceive('getMethod')->andReturn($method);

        $routingMock = \Mockery::mock(Routing::class);
        $routingMock->shouldReceive('getRoute')->with($uriMock)->andReturn($route);

        $routingFactoryMock = \Mockery::mock(RoutingFactory::class);
        $routingFactoryMock->shouldReceive('build')->with(['api1'])->andReturn($routingMock);

        return [
            'serverRequest' => $serverRequestMock,
            'routingFactory' => $routingFactoryMock
        ];
    }

    /**
     * @param EntityInfo $entityInfo
     * @return QueryUtility
     */
    protected function createQueryUtilityMock(EntityInfo $entityInfo)
    {
        $mock = \Mockery::mock(QueryUtility::class);
        $mock->shouldReceive('resolveQueryParams')->with('', $entityInfo)->andReturn([
            'ordering' => null,
            'limit' => null,
            'page' => null,
            'outputLevel' => null
        ]);
        $mock->shouldReceive('resolveQueryParams')->with(
            'integer.gt=123&sort=string.asc&limit=10&page=2&level=5',
            $entityInfo
        )->andReturn([
            'ordering' => \Mockery::mock(OrderingInterface::class),
            'limit' => 10,
            'page' => 2,
            'outputLevel' => 5,
            'constraints' => \Mockery::mock(LogicalInterface::class)
        ]);
        return $mock;
    }

    /**
     * @return EntityInfoStorage
     */
    protected function createEntityInfoStorageMock()
    {
        $entityInfo1 = \Mockery::mock(EntityInfo::class);
        $entityInfo1->shouldReceive('canHandleRequestMode')->withAnyArgs()->andReturn(true);
        $entityInfo2 = \Mockery::mock(EntityInfo::class);
        $entityInfo2->shouldReceive('canHandleRequestMode')->withAnyArgs()->andReturn(false);

        $mock = \Mockery::mock(EntityInfoStorage::class);
        $mock->shouldReceive('get')->with('Entity1')->andReturn($entityInfo1);
        return $mock;
    }

    /**
     * @return Configuration
     */
    protected function createConfigurationMock()
    {
        $mock = \Mockery::mock(Configuration::class);
        $mock->shouldReceive('getApiIdentifiers')->andReturn(['api1']);
        $mock->shouldReceive('getApiConfiguration')->with('api1')->andReturn([
            '1' => [
                'model' => 'Entity1'
            ]
        ]);
        return $mock;
    }

    /**
     * @return Configuration
     */
    protected function createConfigurationMock2()
    {
        $mock = \Mockery::mock(Configuration::class);
        $mock->shouldReceive('getApiIdentifiers')->andReturn(['api1']);
        $mock->shouldReceive('getApiConfiguration')->with('api1')->andReturn([
            '2' => [
                'model' => 'Entity1'
            ]
        ]);
        return $mock;
    }
}
