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
 * Class Request
 *
 * @author Viktor Firus <v@n86.io>
 */
class Request implements RequestInterface
{
    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $apiIdentifier;

    /**
     * @var array
     */
    protected $resourceIds;

    /**
     * @var ConstraintInterface
     */
    protected $constraints;

    /**
     * @var array
     */
    protected $orderings;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $outputLevel;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @var string
     */
    protected $accept;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var string
     */
    protected $controllerClassName;

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Request
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiIdentifier()
    {
        return $this->apiIdentifier;
    }

    /**
     * @param string $apiIdentifier
     * @return Request
     */
    public function setApiIdentifier($apiIdentifier)
    {
        $this->apiIdentifier = $apiIdentifier;
        return $this;
    }

    /**
     * @return array
     */
    public function getResourceIds()
    {
        return $this->resourceIds;
    }

    /**
     * @param array $resourceIds
     * @return Request
     */
    public function setResourceIds(array $resourceIds)
    {
        $this->resourceIds = $resourceIds;
        return $this;
    }

    /**
     * @return ConstraintInterface
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param ConstraintInterface $constraints
     * @return Request
     */
    public function setConstraints(ConstraintInterface $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderings()
    {
        return $this->orderings;
    }

    /**
     * @param array $orderings
     * @return Request
     */
    public function setOrderings(array $orderings)
    {
        $this->orderings = $orderings;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Request
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Request
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getOutputLevel()
    {
        return $this->outputLevel;
    }

    /**
     * @param int $outputLevel
     * @return Request
     */
    public function setOutputLevel($outputLevel)
    {
        $this->outputLevel = $outputLevel;
        return $this;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     * @return Request
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return $this->modelClassName;
    }

    /**
     * @param string $modelClassName
     * @return Request
     */
    public function setModelClassName($modelClassName)
    {
        $this->modelClassName = $modelClassName;
        return $this;
    }

    /**
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->controllerClassName;
    }

    /**
     * @param string $controllerClassName
     * @return Request
     */
    public function setControllerClassName($controllerClassName)
    {
        $this->controllerClassName = $controllerClassName;
        return $this;
    }
}
