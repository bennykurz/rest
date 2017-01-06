<?php declare(strict_types = 1);
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

use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\LimitInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
interface RequestInterface
{
    const REQUEST_MODE_READ = 1;
    const REQUEST_MODE_CREATE = 2;
    const REQUEST_MODE_UPDATE = 4;
    const REQUEST_MODE_PATCH = 8;
    const REQUEST_MODE_DELETE = 16;

    /**
     * @return array
     */
    public function getRoute(): array;

    /**
     * @param array $route
     *
     * @return RequestInterface
     */
    public function setRoute(array $route): RequestInterface;

    /**
     * @return int
     */
    public function getVersion(): int;

    /**
     * @param int $version
     *
     * @return RequestInterface
     */
    public function setVersion(int $version): RequestInterface;

    /**
     * @return string
     */
    public function getApiIdentifier(): string;

    /**
     * @param string $apiIdentifier
     *
     * @return RequestInterface
     */
    public function setApiIdentifier(string $apiIdentifier): RequestInterface;

    /**
     * @return array
     */
    public function getResourceIds(): array;

    /**
     * @param array $resourceIds
     *
     * @return RequestInterface
     */
    public function setResourceIds(array $resourceIds): RequestInterface;

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array;

    /**
     * @param ConstraintInterface[] $constraints
     *
     * @return RequestInterface
     */
    public function setConstraints(array $constraints): RequestInterface;

    /**
     * @return bool
     */
    public function hasOrdering(): bool;

    /**
     * @return OrderingInterface
     */
    public function getOrdering(): OrderingInterface;

    /**
     * @param OrderingInterface $ordering
     *
     * @return RequestInterface
     */
    public function setOrdering(OrderingInterface $ordering): RequestInterface;

    /**
     * @return bool
     */
    public function hasLimit(): bool;

    /**
     * @return LimitInterface
     */
    public function getLimit(): LimitInterface;

    /**
     * @param LimitInterface $limit
     *
     * @return RequestInterface
     */
    public function setLimit(LimitInterface $limit): RequestInterface;

    /**
     * @return int
     */
    public function getOutputLevel(): int;

    /**
     * @param int $outputLevel
     *
     * @return RequestInterface
     */
    public function setOutputLevel(int $outputLevel): RequestInterface;

    /**
     * @return int
     */
    public function getMode(): int;

    /**
     * @param int $mode
     *
     * @return RequestInterface
     */
    public function setMode(int $mode): RequestInterface;

    /**
     * @return string
     */
    public function getModelClassName(): string;

    /**
     * @param string $modelClassName
     *
     * @return RequestInterface
     */
    public function setModelClassName(string $modelClassName): RequestInterface;

    /**
     * @return string
     */
    public function getControllerClassName(): string;

    /**
     * @param string $controllerClassName
     *
     * @return RequestInterface
     */
    public function setControllerClassName(string $controllerClassName): RequestInterface;
}
