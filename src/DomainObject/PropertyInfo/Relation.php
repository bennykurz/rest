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

use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\Persistence\Constraint\ConstraintUtility;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Relation extends AbstractStatic implements RelationInterface
{
    /**
     * @inject
     * @var ConstraintUtility
     */
    protected $constraintUtility;

    /**
     * @var bool
     */
    protected $constraint;

    /**
     * @return bool
     */
    public function isConstraint(): bool
    {
        return $this->constraint ?: false;
    }

    /**
     * @param EntityInterface $entity
     */
    public function castValue(EntityInterface $entity)
    {
        $value = $entity->getProperty($this->getName());
        $isList = substr($this->type, -2) === '[]';
        $type = $isList ? substr($this->type, 0, strlen($this->type) - 2) : $this->type;
        if ($isList && empty(trim($value))) {
            $entity->setProperty($this->getName(), []);

            return;
        } elseif (empty(trim($value))) {
            $entity->setProperty($this->getName(), '');

            return;
        }

        $typeEntityInfo = $this->entityInfoStorage->get($type);

        $repository = $typeEntityInfo->createRepositoryInstance();
        $repository->setConstraints(
            $this->constraintUtility->createResourceIdsConstraints(
                $typeEntityInfo->getUidPropertyInfo(),
                explode(',', $value)
            )
        );

        $result = $repository->read();

        if ($isList) {
            $entity->setProperty($this->getName(), $result);

            return;
        }

        reset($result);
        $entity->setProperty($this->getName(), current($result));
    }

    /**
     * @param string $type
     * @param array  $attributes
     *
     * @return bool
     */
    public static function checkAttributes(string $type, array $attributes = []): bool
    {
        if (!empty($attributes['foreignField'])) {
            return false;
        }

        return self::checkForAbstractEntitySubclass($type);
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    protected static function checkForAbstractEntitySubclass(string $className): bool
    {
        $propertyInfoUtility = new PropertyInfoUtility;

        return ($propertyInfoUtility->checkForAbstractEntitySubclass($className) ||
            $propertyInfoUtility->checkForAbstractEntitySubclass(
                substr($className, 0, strlen($className) - 2)
            ));
    }
}
