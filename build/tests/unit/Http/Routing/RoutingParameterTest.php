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

namespace N86io\Rest\Tests\Unit\Http\Routing;

use N86io\Rest\Http\Routing\RoutingParameter;
use N86io\Rest\UnitTestCase;

/**
 * Class RoutingParameterTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class RoutingParameterTest extends UnitTestCase
{
    public function test()
    {
        $routingParameter = new RoutingParameter('name', 'expr', true, 2);
        $this->assertEquals('name', $routingParameter->getName());
        $this->assertEquals('expr', $routingParameter->getExpression());
        $this->assertTrue($routingParameter->isOptional());
        $this->assertEquals(2, $routingParameter->getTakeResult());
    }
}
