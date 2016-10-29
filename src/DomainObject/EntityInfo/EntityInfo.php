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

use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\ResourceIdInterface;
use N86io\Rest\DomainObject\PropertyInfo\StaticInterface;
use N86io\Rest\Http\RequestInterface;

/**
 * Class EntityInfo
 * @package N86io\Rest\DomainObject\EntityInfo
 */
class EntityInfo implements EntityInfoInterface
{
    /**
     * @var string
     */
    protected $storage;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var PropertyInfoInterface
     */
    protected $resIdPropertyInfo;

    /**
     * @var PropertyInfoInterface
     */
    protected $uidPropertyInfo;

    /**
     * @var bool
     */
    protected $readMode;

    /**
     * @var bool
     */
    protected $writeMode;

    /**
     * @var array
     */
    protected $propertyMapList;

    /**
     * @var array
     */
    protected $propertyInfoList = [];

    /**
     * EntityInfo constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        if (!is_subclass_of($attributes['className'], AbstractEntity::class)) {
            throw new \InvalidArgumentException(
                'Class for EntityInfo should be a subclass of "' . AbstractEntity::class . '".'
            );
        }
        if (array_key_exists('className', $attributes)) {
            $this->className = $attributes['className'];
        }
        if (array_key_exists('storage', $attributes)) {
            $this->storage = $attributes['storage'];
        }
        if (array_key_exists('table', $attributes)) {
            $this->table = $attributes['table'];
        }
        if (array_key_exists('mode', $attributes)) {
            $this->readMode = array_search('read', $attributes['mode']) !== false;
            $this->writeMode = array_search('write', $attributes['mode']) !== false;
        }
    }

    /**
     * @return string
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return PropertyInfoInterface
     */
    public function getResourceIdPropertyInfo()
    {
        return $this->resIdPropertyInfo;
    }

    /**
     * @return PropertyInfoInterface
     */
    public function getUidPropertyInfo()
    {
        return $this->uidPropertyInfo;
    }

    /**
     * @param string $offset
     * @return PropertyInfoInterface
     */
    public function getPropertyInfo($offset)
    {
        return $this->propertyInfoList[$offset];
    }

    /**
     * @return array
     */
    public function getPropertyInfoList()
    {
        return $this->propertyInfoList;
    }

    /**
     * @param PropertyInfoInterface $propertyInfo
     * @throws \Exception
     */
    public function addPropertyInfo(PropertyInfoInterface $propertyInfo)
    {
        if ($this->isResourceIdPropertyInfo($propertyInfo)) {
            $this->resIdPropertyInfo = $propertyInfo;
        }
        if ($this->isUidPropertyInfo($propertyInfo)) {
            $this->uidPropertyInfo = $propertyInfo;
        }
        if ($propertyInfo instanceof StaticInterface) {
            $this->propertyMapList[$propertyInfo->getResourcePropertyName()] = $propertyInfo->getName();
        }
        $this->propertyInfoList[$propertyInfo->getName()] = $propertyInfo;
    }

    /**
     * @param PropertyInfoInterface $propertyInfo
     * @return boolean
     */
    protected function isResourceIdPropertyInfo(PropertyInfoInterface $propertyInfo)
    {
        if (!$propertyInfo instanceof ResourceIdInterface) {
            return false;
        }
        if ($propertyInfo->isResourceId() && !empty($this->resIdPropertyInfo)) {
            throw new \InvalidArgumentException('Only one column can selected as resourceId.');
        }
        return $propertyInfo->isResourceId();
    }

    /**
     * @param PropertyInfoInterface $propertyInfo
     * @return boolean
     */
    protected function isUidPropertyInfo(PropertyInfoInterface $propertyInfo)
    {
        if (!$propertyInfo instanceof StaticInterface) {
            return false;
        }
        if ($propertyInfo->getResourcePropertyName() === 'uid' && !empty($this->uidPropertyInfo)) {
            throw new \InvalidArgumentException('Only one column can selected as uid.');
        }
        return $propertyInfo->getResourcePropertyName() === 'uid';
    }

    /**
     * @param $propertyName
     * @return bool
     */
    public function hasPropertyInfo($propertyName)
    {
        return !empty($this->propertyInfoList[$propertyName]);
    }

    /**
     * @return bool
     */
    public function hasResourceId()
    {
        return $this->resIdPropertyInfo instanceof PropertyInfoInterface;
    }

    /**
     * @return bool
     */
    public function hasUidPropertyInfo()
    {
        return $this->uidPropertyInfo instanceof PropertyInfoInterface;
    }

    /**
     * @param string $name
     * @return string
     */
    public function mapResourcePropertyName($name)
    {
        if (array_key_exists($name, $this->propertyInfoList)) {
            return $name;
        }
        return array_key_exists($name, $this->propertyMapList) ? $this->propertyMapList[$name] : '';
    }

    /**
     * @param int $outputLevel
     * @return array
     */
    public function getVisiblePropertiesOrdered($outputLevel)
    {
        $list = [];
        /** @var PropertyInfoInterface $item */
        foreach ($this->propertyInfoList as $item) {
            if ($item->shouldShow($outputLevel)) {
                $list[str_pad($item->getPosition(), 6, '0', STR_PAD_LEFT) . $item->getName()] = $item;
            }
        }
        ksort($list);
        return array_values($list);
    }

    /**
     * @param string $requestMode
     * @return bool
     */
    public function canHandleRequestMode($requestMode)
    {
        $isReadWriteMode = $this->readMode && $this->writeMode;
        return ($requestMode === RequestInterface::REQUEST_MODE_READ && $this->readMode ||
            $requestMode === RequestInterface::REQUEST_MODE_CREATE && $this->writeMode ||
            $requestMode === RequestInterface::REQUEST_MODE_UPDATE && $isReadWriteMode ||
            $requestMode === RequestInterface::REQUEST_MODE_DELETE && $isReadWriteMode);
    }
}
