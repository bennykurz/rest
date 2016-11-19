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

namespace N86io\Rest\Persistence;

use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\Object\Container;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Constraint\ConstraintUtility;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * Class Repository
 *
 * @author Viktor Firus <v@n86.io>
 */
class Repository implements RepositoryInterface
{
    /**
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @inject
     * @var ConstraintUtility
     */
    protected $constraintUtility;

    /**
     * @var EntityInfoInterface
     */
    protected $entityInfo;

    /**
     * @var ConnectorInterface
     */
    protected $connector;

    /**
     * RepositoryInterface constructor.
     * @param EntityInfoInterface $entityInfo
     */
    public function __construct(EntityInfoInterface $entityInfo)
    {
        $this->entityInfo = $entityInfo;
    }

    public function initializeObject()
    {
        $this->connector = $this->container->get($this->entityInfo->getConnectorClassName(), [$this->entityInfo]);
    }

    /**
     * @param ConstraintInterface $constraints
     * @return RepositoryInterface
     */
    public function setConstraints(ConstraintInterface $constraints)
    {
        $constraints = [$constraints];
        $constraints[] = $this->constraintUtility->createEnableFieldsConstraints($this->entityInfo);
        $constraints = $this->constraintFactory->logicalAnd($constraints);
        $this->connector->setConstraints($constraints);
        return $this;
    }

    /**
     * @param OrderingInterface $ordering
     * @return RepositoryInterface
     */
    public function setOrdering(OrderingInterface $ordering)
    {
        $this->connector->setOrdering($ordering);
        return $this;
    }

    /**
     * @param LimitInterface $limit
     * @return RepositoryInterface
     */
    public function setLimit(LimitInterface $limit)
    {
        $this->connector->setLimit($limit);
        return $this;
    }

    /**
     * @return array
     */
    public function read()
    {
        return $this->connector->read();
    }

    /**
     * @return array
     */
    public function readRaw()
    {
        return $this->connector->readRaw();
    }

    /**
     * @return array
     */
    public function create()
    {
        return $this->connector->create();
    }

    /**
     * @return array
     */
    public function update()
    {
        return $this->connector->update();
    }

    /**
     * @return array
     */
    public function delete()
    {
        return $this->connector->delete();
    }
}
