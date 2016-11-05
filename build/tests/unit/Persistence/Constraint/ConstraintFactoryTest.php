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

namespace N86io\Rest\Tests\Unit\Persistence\Constraint;

use DI\Container;
use Mockery\Mock;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Persistence\Constraint\ComparisonInterface;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\LogicalInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class ConstraintFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class ConstraintFactoryTest extends UnitTestCase
{
    public function testSingle()
    {
        $containerMock = \Mockery::mock(Container::class);
        $containerMock->shouldReceive('make')->withAnyArgs()->andReturn(
            \Mockery::mock(LogicalInterface::class)
        );
        $factory = new ConstraintFactory;
        $this->inject($factory, 'container', $containerMock);

        $this->assertTrue($factory->logicalAnd([]) instanceof LogicalInterface);
        $this->assertTrue($factory->logicalOr([]) instanceof LogicalInterface);


        $containerMock = \Mockery::mock(Container::class);
        $containerMock->shouldReceive('make')->withAnyArgs()->andReturn(
            \Mockery::mock(ComparisonInterface::class)
        );
        $factory = new ConstraintFactory;
        $this->inject($factory, 'container', $containerMock);

        /** @var PropertyInfoInterface $propInfoMock */
        $propInfoMock = \Mockery::mock(PropertyInfoInterface::class);

        $this->assertTrue($factory->lessThan($propInfoMock, '') instanceof ComparisonInterface);
        $this->assertTrue($factory->lessThanOrEqualTo($propInfoMock, '') instanceof ComparisonInterface);
        $this->assertTrue($factory->greaterThan($propInfoMock, '') instanceof ComparisonInterface);
        $this->assertTrue($factory->greaterThanOrEqualTo($propInfoMock, '') instanceof ComparisonInterface);
        $this->assertTrue($factory->equalTo($propInfoMock, '') instanceof ComparisonInterface);
        $this->assertTrue($factory->notEqualTo($propInfoMock, '') instanceof ComparisonInterface);
        $this->assertTrue($factory->contains($propInfoMock, '') instanceof ComparisonInterface);
    }

    public function testStringDetector()
    {
        /** @var PropertyInfoInterface $propInfoMock */
        $propInfoMock = \Mockery::mock(PropertyInfoInterface::class);

        /** @var ConstraintFactory|Mock $factoryMock */
        $factoryMock = \Mockery::mock(ConstraintFactory::class)->makePartial();

        $ltMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('lessThan')->withAnyArgs()->andReturn($ltMock);
        $lteMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('lessThanOrEqualTo')->withAnyArgs()->andReturn($lteMock);
        $gtMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('greaterThan')->withAnyArgs()->andReturn($gtMock);
        $gteMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('greaterThanOrEqualTo')->withAnyArgs()->andReturn($gteMock);
        $eMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('equalTo')->withAnyArgs()->andReturn($eMock);
        $neMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('notEqualTo')->withAnyArgs()->andReturn($neMock);
        $cMock = \Mockery::mock(ComparisonInterface::class);
        $factoryMock->shouldReceive('contains')->withAnyArgs()->andReturn($cMock);

        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'lt', '') === $ltMock);
        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'lte', '') === $lteMock);
        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'gt', '') === $gtMock);
        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'gte', '') === $gteMock);
        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'e', '') === $eMock);
        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'ne', '') === $neMock);
        $this->assertTrue($factoryMock->createComparisonFromStringDetection($propInfoMock, 'c', '') === $cMock);
    }
}
