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

namespace N86io\Rest\Tests\DomainObject;

use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\UnitTestCase;

/**
 * Class AbstractEntityTest
 * @package N86io\Rest\Tests\DomainObject
 */
class AbstractEntityTest extends UnitTestCase
{
    /**
     * @var AbstractEntity
     */
    protected $abstractEntity;

    public function setUp()
    {
        parent::setUp();
        $this->abstractEntity = $this->getMockForAbstractClass(AbstractEntity::class);
        $this->abstractEntity->setProperty('name1', 'value1');
        $this->abstractEntity->setProperty('name2', 'value2');
    }

    public function testGetProperty()
    {
        $this->assertEquals('value1', $this->abstractEntity->getProperty('name1'));
        $this->assertEquals('value2', $this->abstractEntity->getProperty('name2'));
    }

    public function testGetProperties()
    {
        $this->assertEquals(['name1' => 'value1', 'name2' => 'value2'], $this->abstractEntity->getProperties());
    }
}
