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

namespace N86io\Rest\DomainObject\PropertyInfo;

use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInterface;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
interface PropertyInfoInterface
{
    /**
     * @param string $name
     * @param string $type
     * @param array  $attributes
     */
    public function __construct(string $name, string $type, array $attributes);

    /**
     * @return EntityInfoInterface
     */
    public function getEntityInfo(): EntityInfoInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @return string
     */
    public function getGetter(): string;

    /**
     * @return string
     */
    public function getSetter(): string;

    /**
     * @return array
     */
    public function getRawAttributes(): array;

    /**
     * @param int $outputLevel
     *
     * @return bool
     */
    public function shouldShow(int $outputLevel): bool;

    /**
     * @param EntityInterface $entity
     */
    public function castValue(EntityInterface $entity);

    /**
     * @param string $type
     * @param array  $attributes
     *
     * @return bool
     */
    public static function checkAttributes(string $type, array $attributes = []): bool;
}
