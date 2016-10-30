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

namespace N86io\Rest\Tests\Persistence\Constraint;

use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Persistence\Constraint\Comparison;
use N86io\Rest\Persistence\Constraint\ComparisonInterface;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\LogicalInterface;

/**
 * Class ConstraintFactoryTest
 * @package N86io\Rest\Tests\Persistence\Constraint
 */
class ConstraintFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConstraintFactory
     */
    protected $factory;

    /**
     * @var PropertyInfoInterface
     */
    protected $propInfo;

    /**
     * @var ComparisonInterface
     */
    protected $comp;

    public function setUp()
    {
        $this->factory = new ConstraintFactory;
        $this->propInfo = new Common('name', ['type' => 'int']);
        $this->comp = new Comparison($this->propInfo, ComparisonInterface::CONTAINS, '100');
    }

    public function testLogicalAnd()
    {
        /** @var LogicalInterface $logAnd */
        $logAnd = $this->factory->logicalAnd([$this->comp]);
        $this->assertTrue($logAnd instanceof LogicalInterface);
        $this->assertEquals(LogicalInterface::OPERATOR_AND, $logAnd->getType());
    }

    public function testLogicalOr()
    {
        /** @var LogicalInterface $logAnd */
        $logAnd = $this->factory->logicalOr([$this->comp]);
        $this->assertTrue($logAnd instanceof LogicalInterface);
        $this->assertEquals(LogicalInterface::OPERATOR_OR, $logAnd->getType());
    }

    public function testLessThan()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->lessThan($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::LESS_THAN, $comp->getType());
    }

    public function testLessThanOrEqualTo()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->lessThanOrEqualTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::LESS_THAN_OR_EQUAL_TO, $comp->getType());
    }

    public function testGreaterThan()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->greaterThan($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::GREATER_THAN, $comp->getType());
    }

    public function testGreaterThanOrEqualTo()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->greaterThanOrEqualTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::GREATER_THAN_OR_EQUAL_TO, $comp->getType());
    }

    public function testEqualTo()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->equalTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::EQUAL_TO, $comp->getType());
    }

    public function testNotEqualTo()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->notEqualTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::NOT_EQUAL_TO, $comp->getType());
    }

    public function testContains()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->contains($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::CONTAINS, $comp->getType());
    }
}
