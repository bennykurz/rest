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
 * Interface RepositoryQueryInterface
 *
 * @author Viktor Firus <v@n86.io>
 */
interface RepositoryQueryInterface
{
    /**
     * @return EntityInfoInterface
     */
    public function getEntityInfo();

    /**
     * @param EntityInfoInterface $entityInfo
     * @return RepositoryQueryInterface
     */
    public function setEntityInfo(EntityInfoInterface $entityInfo);

    /**
     * @return ConstraintInterface
     */
    public function getConstraints();

    /**
     * @param ConstraintInterface $constraint
     * @return RepositoryQueryInterface
     */
    public function setConstraints(ConstraintInterface $constraint);

    /**
     * @return OrderingInterface
     */
    public function getOrdering();

    /**
     * @param OrderingInterface $ordering
     * @return RepositoryQueryInterface
     */
    public function setOrdering(OrderingInterface $ordering);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $limit
     * @return RepositoryQueryInterface
     */
    public function setLimit($limit);

    /**
     * @return int
     */
    public function getOffset();

    /**
     * @param int $offset
     * @return RepositoryQueryInterface
     */
    public function setOffset($offset);

    /**
     * @return int
     */
    public function getDefaultOffset();

    /**
     * @param int $defaultOffset
     * @return RepositoryQueryInterface
     */
    public function setDefaultOffset($defaultOffset);
}
