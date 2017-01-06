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

namespace N86io\Rest\Tests\Unit\Persistence\Constraint;

use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Persistence\Constraint\Comparison;
use N86io\Rest\Persistence\Constraint\ComparisonInterface;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class ComparisonTest extends UnitTestCase
{
    /**
     * @var Comparison
     */
    protected $comparison;

    public function setUp()
    {
        parent::setUp();
        $this->comparison = new Comparison(
            new Common('name', 'int', []),
            ComparisonInterface::GREATER_THAN,
            '100',
            false
        );
    }

    public function testTypeException1()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Comparison(
            new Common('name', 'int', []),
            0,
            '100',
            true
        );
    }

    public function testTypeException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Comparison(
            new Common('name', 'int', []),
            9,
            '100',
            true
        );
    }

    public function testGetLeftOperand()
    {
        $this->assertTrue($this->comparison->getLeftOperand() instanceof PropertyInfoInterface);
    }

    public function testGetType()
    {
        $this->assertEquals(ComparisonInterface::GREATER_THAN, $this->comparison->getType());
    }

    public function testGetRightOperand()
    {
        $this->assertEquals('100', $this->comparison->getRightOperand());
    }

    public function testIsSave()
    {
        $this->assertFalse($this->comparison->isSave());
    }
}
