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

namespace N86io\Rest\Tests\Persistence\Ordering;

use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\Persistence\Ordering\OrderingFactory;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class OrderingFactoryTest
 * @package N86io\Rest\Tests\Persistence\Ordering
 */
class OrderingFactoryTest extends UnitTestCase
{
    /**
     * @var OrderingFactory
     */
    protected $orderingFactory;

    public function setUp()
    {
        parent::setUp();
        $this->orderingFactory = new OrderingFactory;
    }

    public function test()
    {
        $ordering = $this->orderingFactory->ascending(new Common('name', ['type' => 'int']));
        $this->assertEquals(OrderingInterface::ASCENDING, $ordering->getDirection());

        $ordering = $this->orderingFactory->descending(new Common('name', ['type' => 'int']));
        $this->assertEquals(OrderingInterface::DESCENDING, $ordering->getDirection());
    }
}
