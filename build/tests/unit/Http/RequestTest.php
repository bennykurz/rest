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
use N86io\Rest\UnitTestCase;

/**
 * Class RequestTest
 * @package N86io\Rest\Tests\Unit\Http
 */
class RequestTest extends UnitTestCase
{
    public function test()
    {
        $request = (new Request)
            ->setVersion(1)
            ->setApiIdentifier('test')
            ->setResourceIds([1, 2, 3])
            ->setConstraints($this->getMock(ConstraintInterface::class))
            ->setOrderings(['test' => 'asc'])
            ->setLimit(2)
            ->setPage(3)
            ->setOutputLevel(4)
            ->setMode(5)
            ->setModelClassName('ClassName')
            ->setControllerClassName('ControllerName');
        $this->assertEquals(1, $request->getVersion());
        $this->assertEquals('test', $request->getApiIdentifier());
        $this->assertEquals([1, 2, 3], $request->getResourceIds());
        $this->assertTrue($request->getConstraints() instanceof ConstraintInterface);
        $this->assertEquals(['test' => 'asc'], $request->getOrderings());
        $this->assertEquals(2, $request->getLimit());
        $this->assertEquals(3, $request->getPage());
        $this->assertEquals(4, $request->getOutputLevel());
        $this->assertEquals(5, $request->getMode());
        $this->assertEquals('ClassName', $request->getModelClassName());
        $this->assertEquals('ControllerName', $request->getControllerClassName());
    }
}
