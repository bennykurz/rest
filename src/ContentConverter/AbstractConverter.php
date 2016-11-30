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

namespace N86io\Rest\ContentConverter;

use N86io\Rest\Authorization\AuthorizationInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Exception\InternalServerErrorException;
use Webmozart\Assert\Assert;

/**
 * Class AbstractConverter
 *
 * @author Viktor Firus <v@n86.io>
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
     * @param array $connectorList
     * @param int $outputLevel
     * @return array
     * @throws InternalServerErrorException
     */
    protected function renderRaw(array &$connectorList, $outputLevel)
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
     * @param \DateTime $dateTime
     * @return string
     */
    protected function renderDateTime(\DateTime $dateTime)
    {
        return $dateTime->format('Y-m-d_H:i:s');
    }

    /**
     * @param EntityInterface $entity
     * @param $outputLevel
     * @return array
     */
    protected function renderEntity(EntityInterface $entity, $outputLevel)
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
     * @param PropertyInfoInterface $propertyInfo
     * @return boolean
     */
    protected function hasPropertyAuthorization(PropertyInfoInterface $propertyInfo)
    {
        return $this->authorization->hasPropertyReadAuthorization(
            $propertyInfo->getEntityInfo()->getClassName(),
            $propertyInfo->getName()
        );
    }
}
