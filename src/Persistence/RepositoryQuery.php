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
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * Class RepositoryQuery
 *
 * @author Viktor Firus <v@n86.io>
 */
class RepositoryQuery implements RepositoryQueryInterface
{
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
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $target;

    /**
     * @var int
     */
    protected $defaultMaxItems = 10;

    /**
     * @return EntityInfoInterface
     */
    public function getEntityInfo()
    {
        return $this->entityInfo;
    }

    /**
     * @param EntityInfoInterface $entityInfo
     * @return RepositoryQueryInterface
     */
    public function setEntityInfo(EntityInfoInterface $entityInfo)
    {
        $this->entityInfo = $entityInfo;
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
     * @return RepositoryQueryInterface
     */
    public function setConstraints(ConstraintInterface $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * @return OrderingInterface
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param OrderingInterface $ordering
     * @return RepositoryQueryInterface
     */
    public function setOrdering(OrderingInterface $ordering)
    {
        $this->ordering = $ordering;
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
     * @return RepositoryQueryInterface
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return RepositoryQueryInterface
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultMaxItems()
    {
        return $this->defaultMaxItems;
    }

    /**
     * @param int $defaultMaxItems
     * @return RepositoryQueryInterface
     */
    public function setDefaultMaxItems($defaultMaxItems)
    {
        $this->defaultMaxItems = $defaultMaxItems;
        return $this;
    }
}
