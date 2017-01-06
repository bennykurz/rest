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

namespace N86io\Rest\Persistence;

use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
interface RepositoryInterface
{
    /**
     * RepositoryInterface constructor.
     *
     * @param EntityInfoInterface $entityInfo
     */
    public function __construct(EntityInfoInterface $entityInfo);

    /**
     * @param ConstraintInterface $constraint
     *
     * @return RepositoryInterface
     */
    public function setConstraints(ConstraintInterface $constraint): RepositoryInterface;

    /**
     * @param OrderingInterface $ordering
     *
     * @return RepositoryInterface
     */
    public function setOrdering(OrderingInterface $ordering): RepositoryInterface;

    /**
     * @param LimitInterface $limit
     *
     * @return RepositoryInterface
     */
    public function setLimit(LimitInterface $limit): RepositoryInterface;

    /**
     * @return EntityInterface[]
     */
    public function read(): array;

    /**
     * @return array
     */
    public function readRaw(): array;

    /**
     * @return array
     */
    public function create(): array;

    /**
     * @return array
     */
    public function update(): array;

    /**
     * @return array
     */
    public function delete(): array;
}
