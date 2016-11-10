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

namespace N86io\Rest\Http\Routing;

use N86io\Rest\Object\Container;

/**
 * Class RoutingFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class RoutingFactory implements RoutingFactoryInterface
{
    /**
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @param array $apiIdentifiers
     * @return RoutingInterface
     */
    public function build(array $apiIdentifiers)
    {
        $routing = $this->container->get(RoutingInterface::class);
        $routing->addParameter($this->getVersionRoutingParameter());
        $routing->addParameter($this->getApiIdentifierRouting($apiIdentifiers));
        $routing->addParameter($this->getResourceIdRouting());
        return $routing;
    }

    /**
     * @return RoutingParameterInterface
     */
    public function getVersionRoutingParameter()
    {
        return $this->container->get(
            RoutingParameterInterface::class,
            ['version', '[\w\d]+', true]
        );
    }

    /**
     * @param array $apiIdentifier
     * @return RoutingParameterInterface
     */
    public function getApiIdentifierRouting(array $apiIdentifier)
    {
        return $this->container->get(
            RoutingParameterInterface::class,
            ['apiIdentifier', '(' . implode('|', $apiIdentifier) . ')', false, 2]
        );
    }

    /**
     * @return RoutingParameterInterface
     */
    public function getResourceIdRouting()
    {
        return $this->container->get(
            RoutingParameterInterface::class,
            ['resourceId', '.+', true]
        );
    }
}
