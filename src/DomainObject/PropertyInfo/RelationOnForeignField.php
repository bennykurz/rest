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

use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\Persistence\Constraint\Comparison;
use N86io\Rest\Persistence\Constraint\ComparisonInterface;

/**
 * Class RelationOnForeignField
 *
 * @author Viktor Firus <v@n86.io>
 */
class RelationOnForeignField extends AbstractPropertyInfo implements RelationOnForeignFieldInterface
{
    /**
     * @var string
     */
    protected $foreignField;

    /**
     * RelationOnForeignFieldPropertyInfo constructor.
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, array $attributes)
    {
        if (empty($attributes['foreignField']) || empty(trim($attributes['foreignField']))) {
            throw new \InvalidArgumentException('ForeignField should not empty string.');
        }
        parent::__construct($name, $attributes);
    }

    /**
     * @return string
     */
    public function getForeignField()
    {
        return $this->foreignField;
    }

    /**
     * @param EntityInterface $entity
     */
    public function castValue(EntityInterface $entity)
    {
        $entityInfo = $this->getEntityInfo();
        $uid = $entity->getProperty($entityInfo->getUidPropertyInfo()->getName());
        $isList = substr($this->type, -2) === '[]';
        $type = $isList ? substr($this->type, 0, strlen($this->type) - 2) : $this->type;

        $foreignEntityInfo = $this->entityInfoStorage->get($type);
        $foreignPropertyInfo = $foreignEntityInfo->getPropertyInfo($this->getForeignField());

        $constraints = $this->container->get(Comparison::class, [
            $foreignPropertyInfo,
            ComparisonInterface::INTERNAL_FIND_IN_SET,
            $uid,
            true
        ]);

        $repository = $foreignEntityInfo->createRepositoryInstance();
        $repository->setConstraints($constraints);

        $result = $repository->read();

        if ($isList) {
            $entity->setProperty($this->getName(), $result);
            return;
        }

        reset($result);
        $entity->setProperty($this->getName(), current($result));
    }

    /**
     * @param array $attributes
     * @return boolean
     */
    public static function verifyAttributes(array $attributes)
    {
        if (empty($attributes['foreignField'])) {
            return false;
        }
        return self::checkForAbstractEntitySubclass($attributes['type']);
    }

    /**
     * @param string $className
     * @return boolean
     */
    protected static function checkForAbstractEntitySubclass($className)
    {
        $propertyInfoUtility = new PropertyInfoUtility;
        return ($propertyInfoUtility->checkForAbstractEntitySubclass($className) ||
            $propertyInfoUtility->checkForAbstractEntitySubclass(
                substr($className, 0, strlen($className) - 2)
            ));
    }
}
