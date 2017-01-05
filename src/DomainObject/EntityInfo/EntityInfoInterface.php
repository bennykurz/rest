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

namespace N86io\Rest\DomainObject\EntityInfo;

use N86io\Rest\DomainObject\PropertyInfo\AbstractStatic;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Persistence\RepositoryInterface;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
interface EntityInfoInterface
{
    /**
     * @return string
     */
    public function getConnectorClassName(): string;

    /**
     * @return string
     */
    public function getClassName(): string;

    /**
     * @return string
     */
    public function getTable(): string;

    /**
     * @return PropertyInfoInterface
     */
    public function getResourceIdPropertyInfo(): PropertyInfoInterface;

    /**
     * @return AbstractStatic
     */
    public function getUidPropertyInfo(): AbstractStatic;

    /**
     * @param string $offset
     *
     * @return PropertyInfoInterface
     */
    public function getPropertyInfo(string $offset): PropertyInfoInterface;

    /**
     * @return PropertyInfoInterface[]
     */
    public function getPropertyInfoList(): array;

    /**
     * @param PropertyInfoInterface $propertyInfo
     *
     * @throws \Exception
     */
    public function addPropertyInfo(PropertyInfoInterface $propertyInfo);

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function hasPropertyInfo(string $propertyName): bool;

    /**
     * @return bool
     */
    public function hasResourceIdPropertyInfo(): bool;

    /**
     * @return bool
     */
    public function hasUidPropertyInfo(): bool;

    /**
     * @return JoinInterface[]
     */
    public function getJoins(): array;

    /**
     * @param JoinInterface $join
     */
    public function addJoin(JoinInterface $join);

    /**
     * @param int $outputLevel
     *
     * @return array
     */
    public function getVisiblePropertiesOrdered(int $outputLevel): array;

    /**
     * @param int $requestMode
     *
     * @return bool
     */
    public function canHandleRequestMode(int $requestMode): bool;

    /**
     * @return RepositoryInterface
     */
    public function createRepositoryInstance(): RepositoryInterface;
}
