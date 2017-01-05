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
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
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
     * @param string $className
     * @param string $table
     * @param array  $mode
     * @param string $connector
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $className, string $table, array $mode, string $connector = '')
    {
        if (!is_subclass_of($className, AbstractEntity::class)) {
            throw new \InvalidArgumentException(
                'Class for EntityInfo should be a subclass of "' . AbstractEntity::class . '".'
            );
        }
        $this->className = $className;
        $this->table = $table;
        $this->readMode = array_search('read', $mode) !== false;
        $this->writeMode = array_search('write', $mode) !== false;
        $this->connector = $connector;
    }

    /**
     * @return string
     */
    public function getConnectorClassName(): string
    {
        return $this->connector;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return PropertyInfoInterface
     */
    public function getResourceIdPropertyInfo(): PropertyInfoInterface
    {
        return $this->resIdPropertyInfo;
    }

    /**
     * @return AbstractStatic
     */
    public function getUidPropertyInfo(): AbstractStatic
    {
        return $this->uidPropertyInfo;
    }

    /**
     * @param string $propertyName
     *
     * @return PropertyInfoInterface
     */
    public function getPropertyInfo(string $propertyName): PropertyInfoInterface
    {
        return $this->propertyInfoList[$propertyName];
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function hasPropertyInfo(string $propertyName): bool
    {
        return !empty($this->propertyInfoList[$propertyName]);
    }

    /**
     * @return PropertyInfoInterface[]
     */
    public function getPropertyInfoList(): array
    {
        return $this->propertyInfoList;
    }

    /**
     * @param PropertyInfoInterface $propertyInfo
     *
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
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function isResourceIdPropertyInfo(PropertyInfoInterface $propertyInfo): bool
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
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function isUidPropertyInfo(PropertyInfoInterface $propertyInfo): bool
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
    public function hasResourceIdPropertyInfo(): bool
    {
        return $this->resIdPropertyInfo instanceof PropertyInfoInterface;
    }

    /**
     * @return bool
     */
    public function hasUidPropertyInfo(): bool
    {
        return $this->uidPropertyInfo instanceof PropertyInfoInterface;
    }

    /**
     * @return JoinInterface[]
     */
    public function getJoins(): array
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
     *
     * @return array
     */
    public function getVisiblePropertiesOrdered(int $outputLevel): array
    {
        Assert::greaterThanEq($outputLevel, 0);
        $list = [];
        /** @var PropertyInfoInterface $item */
        foreach ($this->propertyInfoList as $item) {
            if ($item->shouldShow($outputLevel)) {
                $list[str_pad((string)$item->getPosition(), 6, '0', STR_PAD_LEFT) . $item->getName()] = $item;
            }
        }
        ksort($list);

        return array_values($list);
    }

    /**
     * @param int $requestMode
     *
     * @return bool
     */
    public function canHandleRequestMode(int $requestMode): bool
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
     *
     * @return bool
     */
    protected function canReadAndWrite(int $requestMode): bool
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
    public function createRepositoryInstance(): RepositoryInterface
    {
        $connector = $this->container->get(RepositoryInterface::class, $this);

        return $connector;
    }
}
