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

namespace N86io\Rest\Persistence\Constraint;

use N86io\Di\Singleton;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Exception\NoEnableFieldsException;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class ConstraintUtility implements Singleton
{
    /**
     * @inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @param PropertyInfoInterface $propertyInfo
     * @param array                 $resourceIds
     *
     * @return LogicalInterface
     */
    public function createResourceIdsConstraints(
        PropertyInfoInterface $propertyInfo,
        array $resourceIds
    ): LogicalInterface {
        $constraints = [];
        foreach ($resourceIds as $resourceId) {
            $constraints[] = $this->constraintFactory->equalTo($propertyInfo, $resourceId, false);
        }

        return $this->constraintFactory->logicalOr($constraints);
    }

    /**
     * @param EntityInfoInterface $entityInfo
     *
     * @return LogicalInterface
     * @throws NoEnableFieldsException
     */
    public function createEnableFieldsConstraints(EntityInfoInterface $entityInfo): LogicalInterface
    {
        $constraints = [];
        $accessTime = time();
        if ($entityInfo->hasPropertyInfo('deleted')) {
            $deletedPropInfo = $entityInfo->getPropertyInfo('deleted');
            $constraints[] = $this->constraintFactory->equalTo($deletedPropInfo, 0, true);
        }
        if ($entityInfo->hasPropertyInfo('disabled')) {
            $disabledPropInfo = $entityInfo->getPropertyInfo('disabled');
            $constraints[] = $this->constraintFactory->equalTo($disabledPropInfo, 0, true);
        }
        if ($entityInfo->hasPropertyInfo('startTime')) {
            $startTimePropInfo = $entityInfo->getPropertyInfo('startTime');
            $constraints[] = $this->constraintFactory->lessThanOrEqualTo($startTimePropInfo, $accessTime, true);
        }
        if ($entityInfo->hasPropertyInfo('endTime')) {
            $endTimePropInfo = $entityInfo->getPropertyInfo('endTime');
            $constraints[] = $this->constraintFactory->logicalOr([
                $this->constraintFactory->equalTo($endTimePropInfo, 0, true),
                $this->constraintFactory->greaterThan($endTimePropInfo, $accessTime, true)
            ]);
        }
        try {
            return $this->constraintFactory->logicalAnd($constraints);
        } catch (\InvalidArgumentException $e) {
            throw new NoEnableFieldsException;
        }
    }
}
