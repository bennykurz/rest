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

namespace N86io\Rest\Persistence\Ordering;

use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;

/**
 * Class OrderingFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class OrderingFactory implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param PropertyInfoInterface $propertyInfo
     *
     * @return OrderingInterface
     */
    public function ascending(PropertyInfoInterface $propertyInfo)
    {
        return $this->container->get(
            Ordering::class,
            $propertyInfo,
            OrderingInterface::ASCENDING
        );
    }

    /**
     * @param PropertyInfoInterface $propertyInfo
     *
     * @return OrderingInterface
     */
    public function descending(PropertyInfoInterface $propertyInfo)
    {
        return $this->container->get(
            Ordering::class,
            $propertyInfo,
            OrderingInterface::DESCENDING
        );
    }
}
