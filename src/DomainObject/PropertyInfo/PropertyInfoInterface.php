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

namespace N86io\Rest\DomainObject\PropertyInfo;

use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInterface;

/**
 * Interface PropertyInfoInterface
 *
 * @author Viktor Firus <v@n86.io>
 */
interface PropertyInfoInterface
{
    /**
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, array $attributes);

    /**
     * @return EntityInfo
     */
    public function getEntityInfo();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @return string
     */
    public function getGetter();

    /**
     * @return string
     */
    public function getSetter();

    /**
     * @return array
     */
    public function getRawAttributes();

    /**
     * @param int $outputLevel
     * @return boolean
     */
    public function shouldShow($outputLevel);

    /**
     * @param EntityInterface $entity
     */
    public function castValue(EntityInterface $entity);

    /**
     * @param array $attributes
     * @return boolean
     */
    public static function verifyAttributes(array $attributes);
}
