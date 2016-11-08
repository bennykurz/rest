<?php
use DI\Scope;

return [
    \N86io\Rest\Authentication\UserAuthenticationInterface::class =>
        \DI\object(\N86io\Rest\Authentication\UserAuthentication::class)->scope(Scope::SINGLETON),

    \N86io\Rest\Cache\EntityInfoStorageArrayCacheInterface::class =>
        \DI\object(\N86io\Rest\Cache\EntityInfoStorageArrayCache::class)->scope(Scope::PROTOTYPE),

    \N86io\Rest\Cache\EntityInfoStorageCacheInterface::class =>
        \DI\object(\N86io\Rest\Cache\EntityInfoStorageCache::class)->scope(Scope::PROTOTYPE),

    \N86io\Rest\ControllerInterface::class =>
        \DI\object(\N86io\Rest\Controller::class)->scope(Scope::PROTOTYPE),

    \N86io\Rest\DomainObject\EntityInfo\EntityInfoFactoryInterface::class =>
        \DI\object(\N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory::class)->scope(Scope::SINGLETON),

    \N86io\Rest\Http\RequestFactoryInterface::class =>
        \DI\object(\N86io\Rest\Http\RequestFactory::class)->scope(Scope::SINGLETON),

    \N86io\Rest\Http\RequestInterface::class =>
        \DI\object(\N86io\Rest\Http\Request::class)->scope(Scope::PROTOTYPE),

    \N86io\Rest\Http\Routing\RoutingFactoryInterface::class =>
        \DI\object(\N86io\Rest\Http\Routing\RoutingFactory::class)->scope(Scope::SINGLETON),

    \N86io\Rest\Http\Routing\RoutingInterface::class =>
        \DI\object(\N86io\Rest\Http\Routing\Routing::class)->scope(Scope::PROTOTYPE),

    \N86io\Rest\Http\Routing\RoutingParameterInterface::class =>
        \DI\object(\N86io\Rest\Http\Routing\RoutingParameter::class)->scope(Scope::PROTOTYPE),

    \N86io\Rest\Persistence\RepositoryQueryInterface::class =>
        \DI\object(\N86io\Rest\Persistence\RepositoryQuery::class)->scope(Scope::PROTOTYPE),

    \Psr\Http\Message\ResponseInterface::class =>
        \DI\object(\GuzzleHttp\Psr7\Response::class)->scope(Scope::PROTOTYPE),
];
