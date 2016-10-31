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

namespace N86io\Rest\Tests\DomainObject\PropertyInfo;

use N86io\Rest\DomainObject\PropertyInfo\Join;
use N86io\Rest\DomainObject\PropertyInfo\JoinAliasStorage;
use N86io\Rest\ObjectContainer;
use N86io\Rest\UnitTestCase;

/**
 * Class JoinTest
 * @package N86io\Rest\Tests\DomainObject\PropertyInfo
 */
class JoinTest extends UnitTestCase
{
    /**
     * @var Join
     */
    protected $propertyInfo;

    public function setUp()
    {
        parent::setUp();
        $params = [
            'name' => 'somename',
            'attributes' => [
                'type' => 'int',
                'joinTable' => 'test_table',
                'joinCondition' => 'prop = 123'
            ]
        ];
        /** @var JoinAliasStorage $joinAliasStorage */
        $joinAliasStorage = ObjectContainer::get(JoinAliasStorage::class);
        $joinAliasStorage->reset();
        $this->propertyInfo = ObjectContainer::make(Join::class, $params);
    }

    public function testGetTable()
    {
        $this->assertEquals('test_table', $this->propertyInfo->getTable());
    }

    public function testGetAlias()
    {
        $this->assertEquals('j1', $this->propertyInfo->getAlias());
    }

    public function testGetCondition()
    {
        $this->assertEquals('prop = 123', $this->propertyInfo->getCondition());
    }
}
