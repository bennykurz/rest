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

use N86io\Rest\DomainObject\PropertyInfo\AbstractPropertyInfo;
use N86io\Rest\UnitTestCase;

/**
 * Class AbstractPropertyInfoTest
 * @package N86io\Rest\Tests\DomainObject\PropertyInfo
 */
class AbstractPropertyInfoTest extends UnitTestCase
{
    /**
     * @var AbstractPropertyInfo
     */
    protected $abstrPropertyInfo1;

    /**
     * @var AbstractPropertyInfo
     */
    protected $abstrPropertyInfo2;

    public function setUp()
    {
        parent::setUp();
        $attributes1 = [
            'type' => 'int',
            'hide' => false,
            'position' => 3,
            'outputLevel' => 2,
            'getter' => 'getTest'
        ];
        $attributes2 = [
            'type' => 'int',
            'hide' => true,
            'setter' => 'setTest'
        ];
        $this->abstrPropertyInfo1 = $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', $attributes1]);
        $this->abstrPropertyInfo2 = $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', $attributes2]);
    }

    public function testConstructor()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', []]);
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->abstrPropertyInfo1->getName());
    }

    public function testGetType()
    {
        $this->assertEquals('int', $this->abstrPropertyInfo1->getType());
    }

    public function testGetPosition()
    {
        $this->assertEquals(3, $this->abstrPropertyInfo1->getPosition());
        $this->assertEquals(0, $this->abstrPropertyInfo2->getPosition());
    }

    public function testGetGetter()
    {
        $this->assertEquals('getTest', $this->abstrPropertyInfo1->getGetter());
        $this->assertEquals('', $this->abstrPropertyInfo2->getGetter());
    }

    public function testGetSetter()
    {
        $this->assertEquals('', $this->abstrPropertyInfo1->getSetter());
        $this->assertEquals('setTest', $this->abstrPropertyInfo2->getSetter());
    }

    public function testShouldShow()
    {
        $this->assertTrue($this->abstrPropertyInfo1->shouldShow(2));
        $this->assertTrue($this->abstrPropertyInfo1->shouldShow(3));
        $this->assertFalse($this->abstrPropertyInfo1->shouldShow(1));
        $this->assertFalse($this->abstrPropertyInfo1->shouldShow(0));
        $this->assertFalse($this->abstrPropertyInfo1->shouldShow(1));
    }
}
