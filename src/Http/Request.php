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
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Request implements RequestInterface
{
    /**
     * @var array
     */
    protected $route;

    /**
     * @var int
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
     * @var ConstraintInterface[]
     */
    protected $constraints = [];

    /**
     * @var OrderingInterface
     */
    protected $ordering;

    /**
     * @var LimitInterface
     */
    protected $limit;

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
     * @return array
     */
    public function getRoute(): array
    {
        return $this->route;
    }

    /**
     * @param array $route
     *
     * @return RequestInterface
     */
    public function setRoute(array $route): RequestInterface
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return RequestInterface
     */
    public function setVersion(int $version): RequestInterface
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiIdentifier(): string
    {
        return $this->apiIdentifier;
    }

    /**
     * @param string $apiIdentifier
     *
     * @return RequestInterface
     */
    public function setApiIdentifier(string $apiIdentifier): RequestInterface
    {
        $this->apiIdentifier = $apiIdentifier;

        return $this;
    }

    /**
     * @return array
     */
    public function getResourceIds(): array
    {
        return $this->resourceIds;
    }

    /**
     * @param array $resourceIds
     *
     * @return RequestInterface
     */
    public function setResourceIds(array $resourceIds): RequestInterface
    {
        $this->resourceIds = $resourceIds;

        return $this;
    }

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param ConstraintInterface[] $constraints
     *
     * @return RequestInterface
     */
    public function setConstraints(array $constraints): RequestInterface
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrdering(): bool
    {
        return $this->ordering instanceof OrderingInterface;
    }

    /**
     * @return OrderingInterface
     */
    public function getOrdering(): OrderingInterface
    {
        return $this->ordering;
    }

    /**
     * @param OrderingInterface $ordering
     *
     * @return RequestInterface
     */
    public function setOrdering(OrderingInterface $ordering): RequestInterface
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLimit(): bool
    {
        return $this->limit instanceof LimitInterface;
    }

    /**
     * @return LimitInterface
     */
    public function getLimit(): LimitInterface
    {
        return $this->limit;
    }

    /**
     * @param LimitInterface $limit
     *
     * @return RequestInterface
     */
    public function setLimit(LimitInterface $limit): RequestInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getOutputLevel(): int
    {
        return $this->outputLevel;
    }

    /**
     * @param int $outputLevel
     *
     * @return RequestInterface
     */
    public function setOutputLevel(int $outputLevel): RequestInterface
    {
        Assert::greaterThanEq($outputLevel, 0);
        $this->outputLevel = $outputLevel;

        return $this;
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     *
     * @return RequestInterface
     */
    public function setMode(int $mode): RequestInterface
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }

    /**
     * @param string $modelClassName
     *
     * @return RequestInterface
     */
    public function setModelClassName(string $modelClassName): RequestInterface
    {
        $this->modelClassName = $modelClassName;

        return $this;
    }

    /**
     * @return string
     */
    public function getControllerClassName(): string
    {
        return $this->controllerClassName;
    }

    /**
     * @param string $controllerClassName
     *
     * @return RequestInterface
     */
    public function setControllerClassName(string $controllerClassName): RequestInterface
    {
        $this->controllerClassName = $controllerClassName;

        return $this;
    }
}
