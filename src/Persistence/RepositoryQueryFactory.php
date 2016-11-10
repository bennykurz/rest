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

namespace N86io\Rest\Persistence;

use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\Object\Container;
use N86io\Rest\Object\SingletonInterface;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\Constraint\LogicalInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;

/**
 * Class RepositoryQueryFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class RepositoryQueryFactory implements SingletonInterface
{
    /**
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @inject
     * @var EntityInfoStorage
     */
    protected $entityInfoStorage;

    /**
     * @inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @param RequestInterface $request
     * @param array $settings
     * @return RepositoryQueryInterface
     */
    public function build(RequestInterface $request, array $settings)
    {
        $repositoryQuery = $this->container->get(RepositoryQueryInterface::class);
        $entityInfo = $this->entityInfoStorage->get($request->getModelClassName());
        $repositoryQuery->setEntityInfo($entityInfo);
        if (array_key_exists('defaultOffset', $settings)) {
            $repositoryQuery->setDefaultOffset($settings['defaultOffset']);
        }
        $repositoryQuery->setLimit($request->getLimit());
        $repositoryQuery->setOffset($request->getOffset());
        if ($request->getOrdering() instanceof OrderingInterface) {
            $repositoryQuery->setOrdering($request->getOrdering());
        }

        $constraints = [];

        if ($request->getConstraints() instanceof ConstraintInterface) {
            $constraints[] = $request->getConstraints();
        }
        if (!empty($request->getResourceIds())) {
            $constraints[] = $this->createResourceIdsConstraints(
                $entityInfo->getResourceIdPropertyInfo(),
                $request->getResourceIds()
            );
        }
        $constraints[] = $this->createEnableFieldsConstraints($entityInfo);

        $constraints = $this->constraintFactory->logicalAnd($constraints);

        $repositoryQuery->setConstraints($constraints);

        return $repositoryQuery;
    }

    /**
     * @param PropertyInfoInterface $propertyInfo
     * @param array $resourceIds
     * @return LogicalInterface
     */
    protected function createResourceIdsConstraints(PropertyInfoInterface $propertyInfo, array $resourceIds)
    {
        $constraints = [];
        foreach ($resourceIds as $resourceId) {
            $constraints[] = $this->constraintFactory->equalTo($propertyInfo, $resourceId, false);
        }
        return $this->constraintFactory->logicalOr($constraints);
    }

    /**
     * @param EntityInfoInterface $entityInfo
     * @return LogicalInterface
     */
    protected function createEnableFieldsConstraints(EntityInfoInterface $entityInfo)
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
        return $this->constraintFactory->logicalAnd($constraints);
    }
}
