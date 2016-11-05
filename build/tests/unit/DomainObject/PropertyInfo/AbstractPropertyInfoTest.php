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

use N86io\Rest\DomainObject\PropertyInfo\AbstractPropertyInfo;
use N86io\Rest\UnitTestCase;

/**
 * Class AbstractPropertyInfoTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class AbstractPropertyInfoTest extends UnitTestCase
{
    public function test()
    {
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

        /** @var AbstractPropertyInfo $abstrPropertyInfo1 */
        $abstrPropertyInfo1 = $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', $attributes1]);
        /** @var AbstractPropertyInfo $abstrPropertyInfo2 */
        $abstrPropertyInfo2 = $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', $attributes2]);

        $this->assertEquals('test', $abstrPropertyInfo1->getName());

        $this->assertEquals('int', $abstrPropertyInfo1->getType());

        $this->assertEquals(3, $abstrPropertyInfo1->getPosition());
        $this->assertEquals(0, $abstrPropertyInfo2->getPosition());

        $this->assertEquals('getTest', $abstrPropertyInfo1->getGetter());
        $this->assertEquals('', $abstrPropertyInfo2->getGetter());

        $this->assertEquals('', $abstrPropertyInfo1->getSetter());
        $this->assertEquals('setTest', $abstrPropertyInfo2->getSetter());

        $this->assertTrue($abstrPropertyInfo1->shouldShow(2));
        $this->assertTrue($abstrPropertyInfo1->shouldShow(3));
        $this->assertFalse($abstrPropertyInfo1->shouldShow(1));
        $this->assertFalse($abstrPropertyInfo1->shouldShow(0));
        $this->assertFalse($abstrPropertyInfo1->shouldShow(1));
    }

    public function testConstructor()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', []]);
    }
}
