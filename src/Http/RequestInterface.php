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

/**
 * Interface RequestInterface
 * @package N86io\Rest\Http
 */
interface RequestInterface
{
    const REQUEST_MODE_READ = 1;
    const REQUEST_MODE_CREATE = 2;
    const REQUEST_MODE_UPDATE = 3;
    const REQUEST_MODE_DELETE = 4;

    /**
     * @return int
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
     * @return array
     */
    public function getOrderings();

    /**
     * @param array $orderings
     * @return RequestInterface
     */
    public function setOrderings($orderings);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $limit
     * @return RequestInterface
     */
    public function setLimit($limit);

    /**
     * @return int
     */
    public function getPage();

    /**
     * @param int $page
     * @return RequestInterface
     */
    public function setPage($page);

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
    public function getAccept();

    /**
     * @param string $accept
     * @return RequestInterface
     */
    public function setAccept($accept);

    /**
     * @return string
     */
    public function getExtensionKey();

    /**
     * @param string $extensionKey
     * @return RequestInterface
     */
    public function setExtensionKey($extensionKey);

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
