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
use N86io\Rest\Persistence\Constraint\Comparison;
use N86io\Rest\Persistence\Constraint\ComparisonInterface;
use N86io\Rest\Persistence\Constraint\Logical;
use N86io\Rest\Persistence\Constraint\LogicalInterface;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class LogicalTest extends UnitTestCase
{
    /**
     * @var Logical
     */
    protected $logical;

    public function setUp()
    {
        parent::setUp();
        $compConstraints = [
            new Comparison(
                new Common('name1', 'int', []),
                ComparisonInterface::GREATER_THAN,
                '100',
                true
            ),
            new Comparison(
                new Common('name2', 'int', []),
                ComparisonInterface::GREATER_THAN,
                '100',
                true
            )
        ];
        $this->logical = new Logical($compConstraints, LogicalInterface::OPERATOR_AND);
    }

    public function testException1()
    {
        $this->expectException(\InvalidArgumentException::class);
        $compConstraints = [
            new Comparison(
                new Common('name1', 'int', []),
                ComparisonInterface::GREATER_THAN,
                '100',
                true
            )
        ];
        $this->logical = new Logical($compConstraints, 0);
    }


    public function testException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $compConstraints = [
            new Comparison(
                new Common('name1', 'int', []),
                ComparisonInterface::GREATER_THAN,
                '100',
                true
            )
        ];
        $this->logical = new Logical($compConstraints, 3);
    }

    public function testException3()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Logical([], LogicalInterface::OPERATOR_AND);
    }

    public function testException4()
    {
        $this->expectException(\InvalidArgumentException::class);
        $compConstraints = [
            new Comparison(
                new Common('name1', 'int', []),
                ComparisonInterface::GREATER_THAN,
                '100',
                true
            ),
            [
                'something_wrong'
            ]
        ];
        $this->logical = new Logical($compConstraints, LogicalInterface::OPERATOR_AND);
    }

    public function testGetter()
    {
        $this->assertEquals(LogicalInterface::OPERATOR_AND, $this->logical->getType());
        $this->assertEquals('name1', $this->logical->getConstraints()[0]->getLeftOperand()->getName());
    }

    public function testIteration()
    {
        $this->logical->rewind();
        $this->assertTrue($this->logical->valid());
        $this->assertEquals('name1', $this->logical->current()->getLeftOperand()->getName());
        $this->assertEquals(0, $this->logical->key());
        $this->logical->next();
        $this->assertEquals('name2', $this->logical->current()->getLeftOperand()->getName());
        $this->assertEquals(1, $this->logical->key());
        $this->logical->rewind();
    }
}
