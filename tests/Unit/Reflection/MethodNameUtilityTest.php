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

namespace N86io\Rest\Tests\Reflection;

use N86io\Rest\Reflection\MethodNameUtility;
use N86io\Rest\UnitTestCase;

/**
 * Class MethodNameUtilityTest
 * @package N86io\Rest\Tests\Reflection
 */
class MethodNameUtilityTest extends UnitTestCase
{
    /**
     * @var MethodNameUtility
     */
    protected $methodNameUtility;

    public function setUp()
    {
        parent::setUp();
        $this->methodNameUtility = new MethodNameUtility;
    }

    public function testCreatePropertyNameFromMethod()
    {
        $this->assertEquals(
            'somethingYouWant',
            $this->methodNameUtility->createPropertyNameFromMethod('getSomethingYouWant')
        );
        $this->assertEquals(
            'somethingYouWant',
            $this->methodNameUtility->createPropertyNameFromMethod('setSomethingYouWant')
        );
        $this->assertEquals(
            'somethingYouWant',
            $this->methodNameUtility->createPropertyNameFromMethod('isSomethingYouWant')
        );
    }

    public function testIsGetterOrSetter()
    {
        $this->assertTrue($this->methodNameUtility->isGetterOrSetter('setSomething'));
        $this->assertTrue($this->methodNameUtility->isGetterOrSetter('getSomething'));
        $this->assertTrue($this->methodNameUtility->isGetterOrSetter('isSomething'));
        $this->assertFalse($this->methodNameUtility->isGetterOrSetter('createSomething'));
    }

    public function testIsGetter()
    {
        $this->assertFalse($this->methodNameUtility->isGetter('setSomethingYouWant'));
        $this->assertTrue($this->methodNameUtility->isGetter('getSomethingYouWant'));
        $this->assertTrue($this->methodNameUtility->isGetter('isSomethingYouWant'));
        $this->assertFalse($this->methodNameUtility->isGetter('hmmSomethingYouWant'));
    }

    public function testIsSetter()
    {
        $this->assertTrue($this->methodNameUtility->isSetter('setSomethingYouWant'));
        $this->assertFalse($this->methodNameUtility->isSetter('getSomethingYouWant'));
        $this->assertFalse($this->methodNameUtility->isSetter('isSomethingYouWant'));
        $this->assertFalse($this->methodNameUtility->isSetter('hmmSomethingYouWant'));
    }
}
