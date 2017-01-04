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

namespace N86io\Rest\ContentConverter;

use N86io\Rest\Authorization\AuthorizationInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Exception\InternalServerErrorException;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
abstract class AbstractConverter implements ConverterInterface
{
    /**
     * @inject
     * @var EntityInfoStorage
     */
    protected $entityInfoStorage;

    /**
     * @inject
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * Render raw connector list to an array.
     *
     * @param array $connectorList
     * @param int   $outputLevel
     *
     * @return array
     * @throws InternalServerErrorException
     */
    protected function renderRaw(array &$connectorList, int $outputLevel): array
    {
        foreach ($connectorList as &$item) {
            if (is_array($item)) {
                $this->renderRaw($item, $outputLevel);
                continue;
            }
            if ($item instanceof EntityInterface) {
                $item = $this->renderEntity($item, $outputLevel);
                $this->renderRaw($item, $outputLevel);
                continue;
            }
            if ($item instanceof \DateTime) {
                $item = $this->renderDateTime($item);
                continue;
            }
            if (is_object($item)) {
                /** @var ParsableInterface $item */
                Assert::isInstanceOf($item, ParsableInterface::class);
                $item = $item->getParsedValue();
                continue;
            }
        }

        return $connectorList;
    }

    /**
     * Format DateTime to string.
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    protected function renderDateTime(\DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d_H:i:s');
    }

    /**
     * Render an Entity to an array.
     *
     * @param EntityInterface $entity
     * @param int             $outputLevel
     *
     * @return array
     */
    protected function renderEntity(EntityInterface $entity, int $outputLevel): array
    {
        $result = [];
        $entityInfo = $this->entityInfoStorage->get(get_class($entity));
        $visibleProperties = $entityInfo->getVisiblePropertiesOrdered($outputLevel);
        /** @var PropertyInfoInterface $visibleProperty */
        foreach ($visibleProperties as $visibleProperty) {
            if (!$this->hasPropertyAuthorization($visibleProperty)) {
                continue;
            }
            if (!$visibleProperty->getGetter()) {
                $callable = [$entity, 'getProperty'];
                $result[$visibleProperty->getName()] = call_user_func($callable, $visibleProperty->getName());
                continue;
            }
            $callable = [$entity, $visibleProperty->getGetter()];
            $result[$visibleProperty->getName()] = call_user_func($callable, $visibleProperty->getName());
        }

        return $result;
    }

    /**
     * If user is authorized to read given property returns true.
     *
     * @param PropertyInfoInterface $propertyInfo
     *
     * @return bool
     */
    protected function hasPropertyAuthorization(PropertyInfoInterface $propertyInfo): bool
    {
        return $this->authorization->hasPropertyReadAuthorization(
            $propertyInfo->getEntityInfo()->getClassName(),
            $propertyInfo->getName()
        );
    }
}
