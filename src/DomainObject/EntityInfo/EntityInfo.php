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

use N86io\Di\ContainerInterface;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\PropertyInfo\AbstractStatic;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\ResourceIdInterface;
use N86io\Rest\DomainObject\PropertyInfo\UidInterface;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\Persistence\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * Class EntityInfo
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfo implements EntityInfoInterface
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $connector;

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
     * @var AbstractStatic
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
     * @var PropertyInfoInterface[]
     */
    protected $propertyInfoList = [];

    /**
     * @var JoinInterface[]
     */
    protected $joins = [];

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
        if (!empty($attributes['className'])) {
            Assert::string($attributes['className']);
            $this->className = $attributes['className'];
        }
        if (!empty($attributes['connector'])) {
            Assert::string($attributes['connector']);
            $this->connector = $attributes['connector'];
        }
        if (!empty($attributes['table'])) {
            Assert::string($attributes['table']);
            $this->table = $attributes['table'];
        }
        if (!empty($attributes['mode'])) {
            Assert::isArray($attributes['mode']);
            $this->readMode = array_search('read', $attributes['mode']) !== false;
            $this->writeMode = array_search('write', $attributes['mode']) !== false;
        }
    }

    /**
     * @return string
     */
    public function getConnectorClassName()
    {
        return $this->connector;
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
     * @return AbstractStatic
     */
    public function getUidPropertyInfo()
    {
        return $this->uidPropertyInfo;
    }

    /**
     * @param string $propertyName
     * @return PropertyInfoInterface
     */
    public function getPropertyInfo($propertyName)
    {
        Assert::string($propertyName);
        return $this->propertyInfoList[$propertyName];
    }

    /**
     * @param $propertyName
     * @return bool
     */
    public function hasPropertyInfo($propertyName)
    {
        Assert::string($propertyName);
        return !empty($this->propertyInfoList[$propertyName]);
    }

    /**
     * @return PropertyInfoInterface[]
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
        if (!$propertyInfo instanceof UidInterface) {
            return false;
        }
        if ($propertyInfo->isUid() && !empty($this->uidPropertyInfo)) {
            throw new \InvalidArgumentException('Only one column can selected as uid.');
        }
        return $propertyInfo->isUid();
    }

    /**
     * @return bool
     */
    public function hasResourceIdPropertyInfo()
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
     * @return JoinInterface[]
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @param JoinInterface $join
     */
    public function addJoin(JoinInterface $join)
    {
        $this->joins[$join->getAlias()] = $join;
    }

    /**
     * @param int $outputLevel
     * @return array
     */
    public function getVisiblePropertiesOrdered($outputLevel)
    {
        Assert::integer($outputLevel);
        Assert::greaterThanEq($outputLevel, 0);
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
     * @param int $requestMode
     * @return bool
     */
    public function canHandleRequestMode($requestMode)
    {
        Assert::oneOf(
            $requestMode,
            [
                RequestInterface::REQUEST_MODE_READ,
                RequestInterface::REQUEST_MODE_CREATE,
                RequestInterface::REQUEST_MODE_UPDATE,
                RequestInterface::REQUEST_MODE_PATCH,
                RequestInterface::REQUEST_MODE_DELETE
            ]
        );
        return (
            $requestMode === RequestInterface::REQUEST_MODE_READ && $this->readMode ||
            $requestMode === RequestInterface::REQUEST_MODE_CREATE && $this->writeMode ||
            $this->canReadAndWrite($requestMode)
        );
    }

    /**
     * @param int $requestMode
     * @return bool
     */
    protected function canReadAndWrite($requestMode)
    {
        $isReadWriteMode = $this->readMode && $this->writeMode;
        return (
            $requestMode === RequestInterface::REQUEST_MODE_UPDATE && $isReadWriteMode ||
            $requestMode === RequestInterface::REQUEST_MODE_PATCH && $isReadWriteMode ||
            $requestMode === RequestInterface::REQUEST_MODE_DELETE && $isReadWriteMode
        );
    }

    /**
     * @return RepositoryInterface
     */
    public function createRepositoryInstance()
    {
        $connector = $this->container->get(RepositoryInterface::class, $this);
        return $connector;
    }
}
