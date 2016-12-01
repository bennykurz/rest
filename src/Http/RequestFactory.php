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

use N86io\Rest\ControllerInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\Exception\MethodNotAllowedException;
use N86io\Rest\Exception\RequestNotFoundException;
use N86io\Rest\Http\Routing\RoutingFactoryInterface;
use N86io\Rest\Http\Utility\QueryUtility;
use N86io\Rest\Object\Container;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\LimitInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
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
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @inject
     * @var RoutingFactoryInterface
     */
    protected $routingFactory;

    /**
     * @inject
     * @var EntityInfoStorage
     */
    protected $entityInfoStorage;

    /**
     * @inject
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
        $this->checkRoute($route);

        $version = isset($route['version']) ? $route['version'] : '';
        list($modelClassName, $controllerClassName) = $this->resolveClasses(
            $route['apiIdentifier'],
            $version
        );
        $entityInfo = $this->entityInfoStorage->get($modelClassName);
        $this->checkEntityInfo($entityInfo, $serverRequest);

        $query = $serverRequest->getUri()->getQuery() ?: '';
        $queryParams = $this->queryUtility->resolveQueryParams($query, $entityInfo);

        $resourceIds = isset($route['resourceId']) ? explode(',', $route['resourceId']) : [];

        $request = $this->container->get(RequestInterface::class);
        $request->setVersion($version)
            ->setApiIdentifier($route['apiIdentifier'])
            ->setResourceIds($resourceIds)
            ->setOutputLevel($queryParams['outputLevel'])
            ->setModelClassName($modelClassName)
            ->setControllerClassName($controllerClassName)
            ->setMode($this->getRequestMode($serverRequest))
            ->setRoute($route);

        $this->setLimit($request, $queryParams);
        $this->setConstraints($request, $queryParams);
        $this->setOrdering($request, $queryParams);

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param array $queryParams
     */
    protected function setOrdering(RequestInterface $request, array $queryParams)
    {
        if (isset($queryParams['ordering']) &&
            $queryParams['ordering'] instanceof OrderingInterface
        ) {
            $request->setOrdering($queryParams['ordering']);
        }
    }

    /**
     * @param RequestInterface $request
     * @param array $queryParams
     */
    protected function setConstraints(RequestInterface $request, array $queryParams)
    {
        if (isset($queryParams['constraints'])) {
            $request->setConstraints($queryParams['constraints']);
        }
    }

    /**
     * @param RequestInterface $request
     * @param array $queryParams
     */
    protected function setLimit(RequestInterface $request, array $queryParams)
    {
        if ($queryParams['rowCount'] && $queryParams['offset']) {
            $limit = $this->container->get(LimitInterface::class, [$queryParams['offset'], $queryParams['rowCount']]);
            $request->setLimit($limit);
        }
    }

    /**
     * @param EntityInfoInterface $entityInfo
     * @param ServerRequestInterface $serverRequest
     * @throws MethodNotAllowedException
     */
    protected function checkEntityInfo(EntityInfoInterface $entityInfo, ServerRequestInterface $serverRequest)
    {
        if (!$entityInfo->canHandleRequestMode($this->getRequestMode($serverRequest))) {
            throw new MethodNotAllowedException;
        }
    }

    /**
     * @param array $route
     * @throws RequestNotFoundException
     */
    protected function checkRoute(array $route)
    {
        if (empty($route)) {
            throw new RequestNotFoundException;
        }
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
     * @return array
     * @throws RequestNotFoundException
     */
    protected function resolveClasses($apiIdentifier, $version = '')
    {
        $apiConf = $this->configuration->getApiConfiguration($apiIdentifier);

        if (empty($version)) {
            $first = current($apiConf);
            $controller = isset($first['controller']) ? $first['controller'] : ControllerInterface::class;
            return [
                $first['model'],
                $controller
            ];
        }

        if (empty($apiConf[$version])) {
            throw new RequestNotFoundException;
        }

        $controller = isset($apiConf[$version]['controller']) ? $apiConf[$version]['controller'] :
            ControllerInterface::class;
        return [
            $apiConf[$version]['model'],
            $controller
        ];
    }
}
