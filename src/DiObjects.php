<?php
use DI\Scope;
use GuzzleHttp\Psr7\Response;
use N86io\Rest\Cache\EntityInfoStorageArrayCache;
use N86io\Rest\Cache\EntityInfoStorageArrayCacheInterface;
use N86io\Rest\Cache\EntityInfoStorageCache;
use N86io\Rest\Cache\EntityInfoStorageCacheInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactory;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoFactoryInterface;
use N86io\Rest\Http\RequestFactory;
use N86io\Rest\Http\RequestFactoryInterface;
use N86io\Rest\Http\Routing\RoutingFactory;
use N86io\Rest\Http\Routing\RoutingFactoryInterface;
use N86io\Rest\Http\Routing\RoutingParameter;
use N86io\Rest\Http\Routing\RoutingParameterInterface;
use Psr\Http\Message\ResponseInterface;

return [
    EntityInfoStorageArrayCacheInterface::class =>
        \DI\object(EntityInfoStorageArrayCache::class)->scope(Scope::PROTOTYPE),

    EntityInfoStorageCacheInterface::class =>
        \DI\object(EntityInfoStorageCache::class)->scope(Scope::PROTOTYPE),

    EntityInfoFactoryInterface::class =>
        \DI\object(EntityInfoFactory::class),

    RequestFactoryInterface::class =>
        \DI\object(RequestFactory::class),

    RoutingFactoryInterface::class =>
        \DI\object(RoutingFactory::class),

    RoutingParameterInterface::class =>
        \DI\object(RoutingParameter::class),

    ResponseInterface::class =>
        \DI\object(Response::class),
];
