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

namespace N86io\Rest\Tests\Unit\Persistence\Ordering;

use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\Object\Container;
use N86io\Rest\Persistence\Ordering\OrderingFactory;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class OrderingFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class OrderingFactoryTest extends UnitTestCase
{
    public function test()
    {
        $orderingFactory = new OrderingFactory;
        $this->inject($orderingFactory, 'container', $this->createContainerMock());

        $commonMock = \Mockery::mock(Common::class, ['_name_', ['type' => 'int']]);

        $ordering = $orderingFactory->ascending($commonMock);
        $this->assertTrue($ordering instanceof OrderingInterface);

        $ordering = $orderingFactory->descending($commonMock);
        $this->assertTrue($ordering instanceof OrderingInterface);
    }

    /**
     * @return Container
     */
    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->andReturn(\Mockery::mock(OrderingInterface::class));
        return $mock;
    }
}
