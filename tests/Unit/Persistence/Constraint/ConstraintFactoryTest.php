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
use N86io\Rest\UnitTestCase;

/**
 * Class ConstraintFactoryTest
 * @package N86io\Rest\Tests\Persistence\Constraint
 */
class ConstraintFactoryTest extends UnitTestCase
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
        parent::setUp();
        $this->factory = new ConstraintFactory;
        $this->propInfo = new Common('name', ['type' => 'int']);
        $this->comp = new Comparison($this->propInfo, ComparisonInterface::CONTAINS, '100');
    }

    public function testStringDetector()
    {
        /** @var ComparisonInterface $comp */
        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'lt', '100');
        $this->assertEquals(ComparisonInterface::LESS_THAN, $comp->getType());

        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'lte', '100');
        $this->assertEquals(ComparisonInterface::LESS_THAN_OR_EQUAL_TO, $comp->getType());

        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'gt', '100');
        $this->assertEquals(ComparisonInterface::GREATER_THAN, $comp->getType());

        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'gte', '100');
        $this->assertEquals(ComparisonInterface::GREATER_THAN_OR_EQUAL_TO, $comp->getType());

        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'e', '100');
        $this->assertEquals(ComparisonInterface::EQUAL_TO, $comp->getType());

        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'ne', '100');
        $this->assertEquals(ComparisonInterface::NOT_EQUAL_TO, $comp->getType());

        $comp = $this->factory->createComparisonFromStringDetection($this->propInfo, 'c', '100');
        $this->assertEquals(ComparisonInterface::CONTAINS, $comp->getType());
    }

    public function testParticularBuilder()
    {
        /** @var LogicalInterface $logical */
        $logical = $this->factory->logicalAnd([$this->comp]);
        $this->assertTrue($logical instanceof LogicalInterface);
        $this->assertEquals(LogicalInterface::OPERATOR_AND, $logical->getType());

        $logical = $this->factory->logicalOr([$this->comp]);
        $this->assertTrue($logical instanceof LogicalInterface);
        $this->assertEquals(LogicalInterface::OPERATOR_OR, $logical->getType());

        /** @var ComparisonInterface $comp */
        $comp = $this->factory->lessThan($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::LESS_THAN, $comp->getType());

        $comp = $this->factory->lessThanOrEqualTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::LESS_THAN_OR_EQUAL_TO, $comp->getType());

        $comp = $this->factory->greaterThan($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::GREATER_THAN, $comp->getType());

        $comp = $this->factory->greaterThanOrEqualTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::GREATER_THAN_OR_EQUAL_TO, $comp->getType());

        $comp = $this->factory->equalTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::EQUAL_TO, $comp->getType());

        $comp = $this->factory->notEqualTo($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::NOT_EQUAL_TO, $comp->getType());

        $comp = $this->factory->contains($this->propInfo, '100');
        $this->assertTrue($comp instanceof ComparisonInterface);
        $this->assertEquals(ComparisonInterface::CONTAINS, $comp->getType());
    }
}
