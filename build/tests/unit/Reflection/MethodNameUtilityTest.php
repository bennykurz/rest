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

namespace N86io\Rest\Tests\Unit\Reflection;

use N86io\Rest\Reflection\MethodNameUtility;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class MethodNameUtilityTest extends UnitTestCase
{
    public function test()
    {
        $methodNameUtility = new MethodNameUtility;

        $this->assertEquals(
            'somethingYouWant',
            $methodNameUtility->createPropertyNameFromMethod('getSomethingYouWant')
        );
        $this->assertEquals(
            'somethingYouWant',
            $methodNameUtility->createPropertyNameFromMethod('setSomethingYouWant')
        );
        $this->assertEquals(
            'somethingYouWant',
            $methodNameUtility->createPropertyNameFromMethod('isSomethingYouWant')
        );

        $this->assertTrue($methodNameUtility->isGetterOrSetter('setSomething'));
        $this->assertTrue($methodNameUtility->isGetterOrSetter('getSomething'));
        $this->assertTrue($methodNameUtility->isGetterOrSetter('isSomething'));
        $this->assertFalse($methodNameUtility->isGetterOrSetter('createSomething'));

        $this->assertFalse($methodNameUtility->isGetter('setSomethingYouWant'));
        $this->assertTrue($methodNameUtility->isGetter('getSomethingYouWant'));
        $this->assertTrue($methodNameUtility->isGetter('isSomethingYouWant'));
        $this->assertFalse($methodNameUtility->isGetter('hmmSomethingYouWant'));

        $this->assertTrue($methodNameUtility->isSetter('setSomethingYouWant'));
        $this->assertFalse($methodNameUtility->isSetter('getSomethingYouWant'));
        $this->assertFalse($methodNameUtility->isSetter('isSomethingYouWant'));
        $this->assertFalse($methodNameUtility->isSetter('hmmSomethingYouWant'));
    }
}
