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

use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\LimitInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * Interface RequestInterface
 *
 * @author Viktor Firus <v@n86.io>
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
    public function getRoute();

    /**
     * @param array $route
     * @return RequestInterface
     */
    public function setRoute($route);

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param int $version
     * @return RequestInterface
     */
    public function setVersion($version);

    /**
     * @return string
     */
    public function getApiIdentifier();

    /**
     * @param string $apiIdentifier
     * @return RequestInterface
     */
    public function setApiIdentifier($apiIdentifier);

    /**
     * @return array
     */
    public function getResourceIds();

    /**
     * @param array $resourceIds
     * @return RequestInterface
     */
    public function setResourceIds(array $resourceIds);

    /**
     * @return ConstraintInterface
     */
    public function getConstraints();

    /**
     * @param ConstraintInterface $constraints
     * @return RequestInterface
     */
    public function setConstraints(ConstraintInterface $constraints);

    /**
     * @return OrderingInterface
     */
    public function getOrdering();

    /**
     * @param OrderingInterface $ordering
     * @return RequestInterface
     */
    public function setOrdering(OrderingInterface $ordering);

    /**
     * @return LimitInterface
     */
    public function getLimit();

    /**
     * @param LimitInterface $limit
     * @return RequestInterface
     */
    public function setLimit(LimitInterface $limit);

    /**
     * @return int
     */
    public function getOutputLevel();

    /**
     * @param int $outputLevel
     * @return RequestInterface
     */
    public function setOutputLevel($outputLevel);

    /**
     * @return string
     */
    public function getMode();

    /**
     * @param string $mode
     * @return RequestInterface
     */
    public function setMode($mode);

    /**
     * @return string
     */
    public function getModelClassName();

    /**
     * @param string $modelClassName
     * @return RequestInterface
     */
    public function setModelClassName($modelClassName);

    /**
     * @return string
     */
    public function getControllerClassName();

    /**
     * @param string $controllerClassName
     * @return RequestInterface
     */
    public function setControllerClassName($controllerClassName);
}
