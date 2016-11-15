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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo;

use Mockery\MockInterface;
use N86io\Rest\DomainObject\PropertyInfo\Join;
use N86io\Rest\DomainObject\PropertyInfo\JoinAliasStorage;
use N86io\Rest\UnitTestCase;

/**
 * Class JoinTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class JoinTest extends UnitTestCase
{
    public function test()
    {
        $attributes = [
            'type' => 'int',
            'joinTable' => 'test_table',
            'joinCondition' => 'prop = 123'
        ];

        /** @var MockInterface|JoinAliasStorage $mock */
        $mock = \Mockery::mock(JoinAliasStorage::class)
            ->shouldReceive('get')->with('test_table')->andReturn('j1')->getMock();

        $propertyInfo = new Join('somename', $attributes);
        $this->inject($propertyInfo, 'aliasStorage', $mock);

        $this->assertEquals('test_table', $propertyInfo->getTable());
        $this->assertEquals('j1', $propertyInfo->getAlias());
        $this->assertEquals('prop = 123', $propertyInfo->getCondition());

        $this->assertTrue(Join::verifyAttributes($attributes));
        $attr1 = $attributes;
        unset($attr1['joinTable']);
        $this->assertFalse(Join::verifyAttributes($attr1));
        $attr2 = $attributes;
        unset($attr2['joinCondition']);
        $this->assertFalse(Join::verifyAttributes($attr2));
    }
}
