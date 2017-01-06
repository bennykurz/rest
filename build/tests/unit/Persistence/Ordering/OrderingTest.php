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

namespace N86io\Rest\Tests\Unit\Persistence\Ordering;

use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\Persistence\Ordering\Ordering;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class OrderingTest extends UnitTestCase
{
    /**
     * @var Ordering
     */
    protected $ordering;

    public function setUp()
    {
        parent::setUp();
        $this->ordering = new Ordering(
            new Common('name', 'int', []),
            OrderingInterface::ASCENDING
        );
    }

    public function testException1()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->ordering = new Ordering(
            new Common('name', 'int', []),
            0
        );
    }

    public function testException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->ordering = new Ordering(
            new Common('name', 'int', []),
            3
        );
    }

    public function testGetter()
    {
        $this->assertEquals('name', $this->ordering->getPropertyInfo()->getName());
        $this->assertEquals(OrderingInterface::ASCENDING, $this->ordering->getDirection());
    }
}
