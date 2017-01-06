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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo;

use N86io\Rest\DomainObject\PropertyInfo\DynamicSelect;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class DynamicSelectTest extends UnitTestCase
{
    public function test()
    {
        $attributes = [
            'ordering'   => true,
            'constraint' => false,
            'select'     => 'thisIsNotRealSqlExpression',
        ];
        $propertyInfo = new DynamicSelect('testSomething', 'int', $attributes);

        $this->assertTrue($propertyInfo->isOrdering());
        $this->assertFalse($propertyInfo->isConstraint());
        $this->assertEquals('thisIsNotRealSqlExpression', $propertyInfo->getSelect());

        $this->assertTrue(DynamicSelect::checkAttributes('int', $attributes));
        unset($attributes['select']);
        $this->assertFalse(DynamicSelect::checkAttributes('int', $attributes));
    }

    public function testConstructor()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DynamicSelect('testSomething', '', []);
    }
}
