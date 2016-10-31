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

namespace N86io\Rest\Http\Utility;

use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\RestrictableInterface;
use N86io\Rest\DomainObject\PropertyInfo\SortableInterface;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Ordering\OrderingFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class QueryUtility
 * @package N86io\Rest\Http\Utility
 */
class QueryUtility
{
    /**
     * @Inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @param ServerRequestInterface $serverRequest
     * @param EntityInfoInterface $entityInfo
     * @return array
     */
    public function resolveQueryParams(ServerRequestInterface $serverRequest, EntityInfoInterface $entityInfo)
    {
        parse_str($serverRequest->getUri()->getQuery(), $parsed);
        $queryParams = $parsed ?: [];
        $result = [
            'ordering' => [],
            'limit' => null,
            'page' => null,
            'outputLevel' => null
        ];
        $constraints = [];
        foreach ($queryParams as $name => $value) {
            switch ($name) {
                case 'sort':
                    $this->createOrdering($result['ordering'], $value, $entityInfo);
                    break;
                case 'limit':
                    $result['limit'] = $this->parseNumericValue($value);
                    break;
                case 'page':
                    $result['page'] = $this->parseNumericValue($value);
                    break;
                case 'level':
                    $result['outputLevel'] = $this->parseNumericValue($value);
                    break;
                default:
                    list($propertyName, $operator) = explode('_', $name);
                    $this->createConstraint($constraints, $entityInfo, $propertyName, $operator, $value);
            }
        }
        if (!empty($constraints)) {
            $result['constraints'] = $this->constraintFactory->logicalAnd($constraints);
        }
        return $result;
    }

    /**
     * @param string $value
     * @return int
     */
    protected function parseNumericValue($value)
    {
        return is_numeric($value) ? intval($value) : null;
    }

    /**
     * @param array $ordering
     * @param string $propNameAndDirection
     * @param EntityInfoInterface $entityInfo
     */
    protected function createOrdering(array &$ordering, $propNameAndDirection, EntityInfoInterface $entityInfo)
    {
        list($propertyName, $direction) = explode('.', $propNameAndDirection);
        if (!$entityInfo->hasPropertyInfo($propertyName)) {
            return;
        }
        $orderingFactory = new OrderingFactory;
        $propertyInfo = $entityInfo->getPropertyInfo($propertyName);
        if ($propertyInfo instanceof SortableInterface && $propertyInfo->isOrdering()) {
            switch ($direction) {
                case 'desc':
                    $ordering[] = $orderingFactory->descending($propertyInfo);
                    break;
                case 'asc':
                default:
                    $ordering[] = $orderingFactory->ascending($propertyInfo);
            }
        }
    }

    /**
     * @param array $constraints
     * @param EntityInfoInterface $entityInfo
     * @param string $propertyName
     * @param string $operator
     * @param string $value
     */
    protected function createConstraint(
        array &$constraints,
        EntityInfoInterface $entityInfo,
        $propertyName,
        $operator,
        $value
    ) {
        if (!$entityInfo->hasPropertyInfo($propertyName)) {
            return;
        }
        $propertyInfo = $entityInfo->getPropertyInfo($propertyName);
        if (!$propertyInfo instanceof RestrictableInterface || !$propertyInfo->isConstraint()) {
            return;
        }

        $constraints[] = $this->constraintFactory->createComparisonFromStringDetection(
            $propertyInfo,
            $operator,
            $value,
            false
        );
    }
}
