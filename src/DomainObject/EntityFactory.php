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

namespace N86io\Rest\DomainObject;

use N86io\Di\Container;
use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;

/**
 * Class EntityFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityFactory implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $entityMemory;

    /**
     * @param EntityInfoInterface $entityInfo
     * @param array $dbRows
     * @return EntityInterface[]
     */
    public function buildList(EntityInfoInterface $entityInfo, array $dbRows)
    {
        $result = [];
        foreach ($dbRows as $dbRow) {
            $result[] = $this->build($entityInfo, $dbRow);
        }
        return $result;
    }

    /**
     * @param EntityInfoInterface $entityInfo
     * @param array $dbRow
     * @return EntityInterface
     */
    public function build(EntityInfoInterface $entityInfo, array $dbRow)
    {
        $entityClassName = $entityInfo->getClassName();
        $entityUid = $dbRow[$entityInfo->getUidPropertyInfo()->getName()];
        $entity = &$this->entityMemory[$entityClassName][$entityUid];

        if (!$entity instanceof EntityInterface) {
            /** @var AbstractEntity $entity */
            $entity = $this->container->get($entityInfo->getClassName());
            foreach ($dbRow as $propertyName => $value) {
                if (!$entityInfo->hasPropertyInfo($propertyName)) {
                    continue;
                }
                $entity->setProperty($propertyName, $value);
            }
            foreach ($entityInfo->getPropertyInfoList() as $propertyInfo) {
                $propertyInfo->castValue($entity);
            }
        }
        return $entity;
    }
}
