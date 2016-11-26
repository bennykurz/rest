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

use N86io\Rest\Authentication\AuthenticationConfiguration;
use N86io\Rest\Authentication\AuthenticationInterface;
use N86io\Rest\Cache\ContainerCacheInterface;
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
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @inject
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $serverRequest)
    {
        $this->authentication->load();

        $requestFactory = $this->container->get(RequestFactoryInterface::class);
        $responseFactory = $this->container->get(ResponseFactory::class);
        $responseFactory->setServerRequest($serverRequest);

        try {
            $request = $requestFactory->fromServerRequest($serverRequest);
        } catch (\Exception $e) {
            return $responseFactory->errorRequest($e->getCode());
        }

        if (!$this->authentication->hasApiAccess($request->getModelClassName(), $request->getMode())) {
            return $responseFactory->unauthorized();
        }

        try {
            return $this->result($request);
        } catch (\Exception $e) {
            return $responseFactory->errorRequest($e->getCode());
        }
    }

    /**
     * @param ContainerCacheInterface $containerCache
     * @param array $classMapping
     * @param AuthenticationConfiguration $authConf
     * @return Bootstrap
     */
    public static function initialize(
        AuthenticationConfiguration $authConf = null,
        ContainerCacheInterface $containerCache = null,
        array $classMapping = []
    ) {
        Container::initializeContainer($containerCache, $classMapping);
        $container = Container::makeInstance(Container::class);
        $self = $container->get(Bootstrap::class);
        $self->authentication->setConfiguration($authConf);

        return $self;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function result(RequestInterface $request)
    {
        return $this->container->get(ControllerInterface::class)->process($request);
    }
}
