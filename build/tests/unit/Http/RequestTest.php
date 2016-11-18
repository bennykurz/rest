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

namespace N86io\Rest\Tests\Unit\Http;

use N86io\Rest\Http\Request;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\LimitInterface;
use N86io\Rest\Persistence\Ordering\OrderingInterface;
use N86io\Rest\UnitTestCase;

/**
 * Class RequestTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class RequestTest extends UnitTestCase
{
    public function test()
    {
        $constraint = \Mockery::mock(ConstraintInterface::class);
        $ordering = \Mockery::mock(OrderingInterface::class);
        $limit = \Mockery::mock(LimitInterface::class);
        $request = (new Request)
            ->setVersion('1')
            ->setApiIdentifier('test')
            ->setResourceIds([1, 2, 3])
            ->setConstraints($constraint)
            ->setOrdering($ordering)
            ->setLimit($limit)
            ->setOutputLevel(4)
            ->setMode(5)
            ->setModelClassName('ClassName')
            ->setControllerClassName('ControllerName')
            ->setRoute(['route']);
        $this->assertEquals(1, $request->getVersion());
        $this->assertEquals('test', $request->getApiIdentifier());
        $this->assertEquals([1, 2, 3], $request->getResourceIds());
        $this->assertSame($constraint, $request->getConstraints());
        $this->assertSame($ordering, $request->getOrdering());
        $this->assertSame($limit, $request->getLimit());
        $this->assertEquals(4, $request->getOutputLevel());
        $this->assertEquals(5, $request->getMode());
        $this->assertEquals('ClassName', $request->getModelClassName());
        $this->assertEquals('ControllerName', $request->getControllerClassName());
        $this->assertEquals(['route'], $request->getRoute());
    }
}
