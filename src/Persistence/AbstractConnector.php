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

use N86io\Rest\DomainObject\EntityFactory;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * Class AbstractConnector
 *
 * @author Viktor Firus <v@n86.io>
 */
abstract class AbstractConnector implements ConnectorInterface
{
    /**
     * @inject
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var EntityInfoInterface
     */
    protected $entityInfo;

    /**
     * @var ConstraintInterface
     */
    protected $constraints;

    /**
     * @var OrderingInterface
     */
    protected $ordering;

    /**
     * @var LimitInterface
     */
    protected $limit;

    /**
     * AbstractConnector constructor.
     * @param EntityInfoInterface $entityInfo
     */
    public function __construct(EntityInfoInterface $entityInfo)
    {
        $this->entityInfo = $entityInfo;
    }

    /**
     * @return array
     */
    public function read()
    {
        return $this->entityFactory->buildList(
            $this->entityInfo,
            $this->readRaw()
        );
    }

    /**
     * @param ConstraintInterface $constraints
     * @return ConnectorInterface
     */
    public function setConstraints(ConstraintInterface $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * @param OrderingInterface $ordering
     * @return ConnectorInterface
     */
    public function setOrdering(OrderingInterface $ordering)
    {
        $this->ordering = $ordering;
        return $this;
    }

    /**
     * @param LimitInterface $limit
     * @return ConnectorInterface
     */
    public function setLimit(LimitInterface $limit)
    {
        $this->limit = $limit;
        return $this;
    }
}
