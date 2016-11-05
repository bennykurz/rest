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

namespace N86io\Rest\Http;

use DI\Container;
use N86io\Rest\ControllerInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\Exception\InvalidRequestException;
use N86io\Rest\Http\Routing\RoutingFactoryInterface;
use N86io\Rest\Http\Utility\QueryUtility;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Service\Configuration;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @Inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @Inject
     * @var RoutingFactoryInterface
     */
    protected $routingFactory;

    /**
     * @Inject
     * @var EntityInfoStorage
     */
    protected $entityInfoStorage;

    /**
     * @Inject
     * @var QueryUtility
     */
    protected $queryUtility;

    /**
     * @param ServerRequestInterface $serverRequest
     * @return RequestInterface
     */
    public function fromServerRequest(ServerRequestInterface $serverRequest)
    {
        $routing = $this->routingFactory->build($this->configuration->getApiIdentifiers());

        $route = $routing->getRoute($serverRequest->getUri());

        if (empty($route)) {
            throw new InvalidRequestException;
        }

        $version = array_key_exists('version', $route) ? $route['version'] : '';
        list($modelClassName, $controllerClassName) = $this->resolveClasses(
            $route['apiIdentifier'],
            $version
        );
        $entityInfo = $this->entityInfoStorage->get($modelClassName);

        $queryParams = $this->queryUtility->resolveQueryParams($serverRequest->getUri()->getQuery(), $entityInfo);

        $resourceIds = array_key_exists('resourceId', $route) ? explode(',', $route['resourceId']) : [];

        /** @var RequestInterface $request */
        $request = $this->container->make(RequestInterface::class);
        $request->setVersion($version)
            ->setApiIdentifier($route['apiIdentifier'])
            ->setResourceIds($resourceIds)
            ->setOrderings($queryParams['ordering'])
            ->setLimit($queryParams['limit'])
            ->setPage($queryParams['page'])
            ->setOutputLevel($queryParams['outputLevel'])
            ->setModelClassName($modelClassName)
            ->setControllerClassName($controllerClassName)
            ->setMode($this->getRequestMode($serverRequest));

        if (array_key_exists('constraints', $queryParams) &&
            $queryParams['constraints'] instanceof ConstraintInterface
        ) {
            $request->setConstraints($queryParams['constraints']);
        }

        return $request;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return int
     */
    protected function getRequestMode(ServerRequestInterface $serverRequest)
    {
        switch ($serverRequest->getMethod()) {
            case 'POST':
                return RequestInterface::REQUEST_MODE_CREATE;
            case 'PATCH':
                return RequestInterface::REQUEST_MODE_PATCH;
            case 'PUT':
                return RequestInterface::REQUEST_MODE_UPDATE;
            case 'DELETE':
                return RequestInterface::REQUEST_MODE_DELETE;
        }
        // GET or some other
        return RequestInterface::REQUEST_MODE_READ;
    }

    /**
     * @param string $apiIdentifier
     * @param string $version
     * @return string
     */
    protected function resolveClasses($apiIdentifier, $version = '')
    {
        $apiConf = $this->configuration->getApiConfiguration($apiIdentifier);

        if (empty($version)) {
            $first = current($apiConf);
            $controller = array_key_exists('controller', $first) ? $first['controller'] : ControllerInterface::class;
            return [
                $first['model'],
                $controller
            ];
        }

        if (!array_key_exists($version, $apiConf)) {
            throw new InvalidRequestException;
        }

        $controller = array_key_exists('controller', $apiConf[$version]) ? $apiConf[$version]['controller'] :
            ControllerInterface::class;
        return [
            $apiConf[$version]['model'],
            $controller
        ];
    }
}
