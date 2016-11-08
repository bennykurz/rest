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

namespace N86io\Rest\DomainObject\EntityInfo;

use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;

/**
 * Interface EntityInfoInterface
 *
 * @author Viktor Firus <v@n86.io>
 */
interface EntityInfoInterface
{
    /**
     * @return string
     */
    public function getStorage();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @return array
     */
    public function getEnableFields();

    /**
     * @return PropertyInfoInterface
     */
    public function getResourceIdPropertyInfo();

    /**
     * @return PropertyInfoInterface
     */
    public function getUidPropertyInfo();

    /**
     * @param string $offset
     * @return PropertyInfoInterface
     */
    public function getPropertyInfo($offset);

    /**
     * @return array
     */
    public function getPropertyInfoList();

    /**
     * @param PropertyInfoInterface $propertyInfo
     * @throws \Exception
     */
    public function addPropertyInfo(PropertyInfoInterface $propertyInfo);

    /**
     * @param $propertyName
     * @return bool
     */
    public function hasPropertyInfo($propertyName);

    /**
     * @return bool
     */
    public function hasResourceIdPropertyInfo();

    /**
     * @return bool
     */
    public function hasUidPropertyInfo();

    /**
     * @param string $name
     * @return string
     */
    public function mapResourcePropertyName($name);

    /**
     * @param int $outputLevel
     * @return array
     */
    public function getVisiblePropertiesOrdered($outputLevel);

    /**
     * @param string $requestMode
     * @return bool
     */
    public function canHandleRequestMode($requestMode);
}
