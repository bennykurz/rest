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
use N86io\Rest\Exception\InvalidOrderingException;
use N86io\Rest\Object\Container;
use N86io\Rest\Object\Singleton;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Ordering\OrderingFactory;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use Webmozart\Assert\Assert;

/**
 * Class QueryUtility
 *
 * @author Viktor Firus <v@n86.io>
 */
class QueryUtility implements Singleton
{
    /**
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @param string $queryParams
     * @param EntityInfoInterface $entityInfo
     * @return array
     */
    public function resolveQueryParams($queryParams, EntityInfoInterface $entityInfo)
    {
        Assert::string($queryParams);
        parse_str($queryParams, $parsed);
        $parsed = $parsed ?: [];
        $result = [
            'rowCount' => null,
            'offset' => null,
            'outputLevel' => 0
        ];
        $constraints = [];
        foreach ($parsed as $name => $value) {
            switch ($name) {
                case 'sort':
                    try {
                        $result['ordering'] = $this->createOrdering($value, $entityInfo);
                    } catch (InvalidOrderingException $e) {
                        // Nothing to do, because no or invalid ordering.
                    }
                    break;
                case 'rowCount':
                    $result['rowCount'] = $this->parseNumericValue($value);
                    break;
                case 'offset':
                    $result['offset'] = $this->parseNumericValue($value);
                    break;
                case 'level':
                    $result['outputLevel'] = $this->parseOutputLevel($value);
                    break;
                default:
                    list($propertyName, $operator) = explode('_', $name);
                    $this->createConstraint($constraints, $entityInfo, $propertyName, $operator, $value);
            }
        }
        if (!empty($constraints)) {
            $result['constraints'] = $constraints;
        }
        return $result;
    }

    protected function parseOutputLevel($outputLevel)
    {
        $outputLevel = $this->parseNumericValue($outputLevel) ?: 0;
        return $outputLevel >= 0 ? $outputLevel : 0;
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
     * @param string $propNameAndDirection
     * @param EntityInfoInterface $entityInfo
     * @return OrderingInterface
     * @throws InvalidOrderingException
     */
    protected function createOrdering($propNameAndDirection, EntityInfoInterface $entityInfo)
    {
        list($propertyName, $direction) = explode('.', $propNameAndDirection);
        if (!$entityInfo->hasPropertyInfo($propertyName)) {
            throw new InvalidOrderingException;
        }
        $orderingFactory = $this->container->get(OrderingFactory::class);
        $propertyInfo = $entityInfo->getPropertyInfo($propertyName);
        if ($propertyInfo instanceof SortableInterface && $propertyInfo->isOrdering()) {
            switch ($direction) {
                case 'desc':
                    return $orderingFactory->descending($propertyInfo);
                    break;
                case 'asc':
                default:
                    return $orderingFactory->ascending($propertyInfo);
            }
        }
        throw new InvalidOrderingException;
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
