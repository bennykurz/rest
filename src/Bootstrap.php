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

namespace N86io\Rest;

use N86io\Rest\Authentication\AuthenticationInterface;
use N86io\Rest\Authorization\Authorization;
use N86io\Rest\Authorization\AuthorizationInterface;
use N86io\Rest\Cache\ContainerCache;
use N86io\Rest\Cache\ContainerCacheInterface;
use N86io\Rest\Exception\BootstrapException;
use N86io\Rest\Exception\ContainerException;
use N86io\Rest\Http\RequestFactoryInterface;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\Http\ResponseFactory;
use N86io\Rest\Object\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Bootstrap
 *
 * @author Viktor Firus <v@n86.io>
 */
class Bootstrap
{
    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * @var BootstrapHooks
     */
    protected $hooks;

    /**
     * Bootstrap constructor.
     * @param ServerRequestInterface $serverRequest
     * @param BootstrapHooks $bootstrapHooks
     */
    public function __construct(ServerRequestInterface $serverRequest, BootstrapHooks $bootstrapHooks = null)
    {
        $this->serverRequest = $serverRequest;
        $this->hooks = $bootstrapHooks ?: new BootstrapHooks;
    }

    /**
     * @return ResponseFactory
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @return Authorization
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @return bool|ResponseInterface
     */
    public function run()
    {
        $this->hooks->runFirstRun($this);

        $this->initializeContainer();
        $this->hooks->runAfterInitializeContainer($this);

        if (($result = $this->initializeRequest()) !== true) {
            return $result;
        }
        $this->hooks->runAfterInitializeRequest($this);

        $this->initializeAuthentication();
        $this->hooks->runAfterInitializeAuthentication($this);

        if (($result = $this->checkAuthorization()) !== true) {
            return $result;
        }
        $this->hooks->runAfterCheckAuthorization($this);

        return $this->createResult();
    }

    /**
     * @param array $classMapping
     * @param ContainerCacheInterface $containerCache
     */
    public function initializeContainer(
        array $classMapping = [],
        ContainerCacheInterface $containerCache = null
    ) {
        try {
            $containerCache = $containerCache ?: new ContainerCache;
            Container::initialize($containerCache, $classMapping);
        } catch (ContainerException $e) {
            // Nothing to do, if container already initialized
        }
        if (!$this->container) {
            $this->container = Container::makeInstance(Container::class);
        }
    }

    /**
     * @return bool|ResponseInterface
     * @throws BootstrapException
     */
    public function initializeRequest()
    {
        if (!$this->container) {
            throw new BootstrapException('Container should be initialized before.');
        }
        if (!$this->requestFactory) {
            $this->requestFactory = $this->container->get(RequestFactoryInterface::class);
            $this->responseFactory = $this->container->get(ResponseFactory::class);
            $this->responseFactory->setServerRequest($this->serverRequest);
        }
        if (!$this->request) {
            try {
                $this->request = $this->requestFactory->fromServerRequest($this->serverRequest);
            } catch (\Exception $e) {
                return $this->responseFactory->errorCode($e->getCode());
            }
        }
        return true;
    }

    /**
     * @return AuthenticationInterface
     * @throws BootstrapException
     */
    public function initializeAuthentication()
    {
        if (!$this->request) {
            throw new BootstrapException('Request should be initialized before.');
        }
        if (!$this->authentication) {
            $this->authentication = Container::makeInstance(AuthenticationInterface::class);
            $this->authentication->load();
        }
        return $this->authentication;
    }

    /**
     * @return bool|ResponseInterface
     * @throws BootstrapException
     */
    public function checkAuthorization()
    {
        if (!$this->authentication) {
            throw new BootstrapException('Authentication should be initialized before.');
        }
        if (!$this->authorization) {
            $this->authorization = Container::makeInstance(AuthorizationInterface::class);
        }
        if (!$this->authorization->hasApiAccess($this->request->getModelClassName(), $this->request->getMode())) {
            return $this->responseFactory->unauthorized();
        }
        return true;
    }

    /**
     * @return ResponseInterface
     * @throws BootstrapException
     */
    public function createResult()
    {
        if (!$this->authorization) {
            throw new BootstrapException('Authorization check should be done before.');
        }
        try {
            return $this->container->get($this->request->getControllerClassName())->process($this->request);
        } catch (\Exception $e) {
            return $this->responseFactory->errorCode($e->getCode());
        }
    }
}
