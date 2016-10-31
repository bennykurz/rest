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

use N86io\Rest\DomainObject\PropertyInfo\JoinAliasStorage;
use N86io\Rest\ObjectContainer;
use N86io\Rest\UnitTestCase;

/**
 * Class JoinAliasStorageTest
 * @package N86io\Rest\Tests\DomainObject\PropertyInfo
 */
class JoinAliasStorageTest extends UnitTestCase
{
    public function testGet()
    {
        /** @var JoinAliasStorage $joinAliasStorage */
        $joinAliasStorage = ObjectContainer::get(JoinAliasStorage::class);
        $joinAliasStorage->reset();
        $this->assertEquals('j1', $joinAliasStorage->get('tableA'));
        $this->assertEquals('j2', $joinAliasStorage->get('tableB'));
        $joinAliasStorage = ObjectContainer::get(JoinAliasStorage::class);
        $this->assertEquals('j1', $joinAliasStorage->get('tableA'));
        $this->assertEquals('j3', $joinAliasStorage->get('tableC'));
        $joinAliasStorage = ObjectContainer::get(JoinAliasStorage::class);
        $this->assertEquals('j2', $joinAliasStorage->get('tableB'));
    }
}
