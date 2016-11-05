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

namespace N86io\Rest\Tests\Unit\Reflection;

use N86io\Rest\Reflection\VarTypeUtility;
use N86io\Rest\UnitTestCase;

/**
 * Class VarTypeUtilityTest
 * @package N86io\Rest\Tests\Unit\Reflection
 */
class VarTypeUtilityTest extends UnitTestCase
{
    /**
     * @var VarTypeUtility
     */
    protected $varTypeUtility;

    public function setUp()
    {
        parent::setUp();
        $this->varTypeUtility = new VarTypeUtility;
    }

    public function testIsDefaultType()
    {
        $this->assertEquals(true, $this->varTypeUtility->isDefaultType('int'));
        $this->assertEquals(true, $this->varTypeUtility->isDefaultType('float'));
        $this->assertEquals(true, $this->varTypeUtility->isDefaultType('string'));
        $this->assertEquals(true, $this->varTypeUtility->isDefaultType('array'));
        $this->assertEquals(true, $this->varTypeUtility->isDefaultType('\DateTime'));
    }

    public function testIsDateTime()
    {
        $this->assertEquals(true, $this->varTypeUtility->isDateTime('DateTime'));
        $this->assertEquals(true, $this->varTypeUtility->isDateTime('\DateTime'));
        $this->assertEquals(false, $this->varTypeUtility->isDateTime('int'));
    }

    public function testIsBoolean()
    {
        $this->assertEquals(true, $this->varTypeUtility->isBoolean('bool'));
        $this->assertEquals(true, $this->varTypeUtility->isBoolean('boolean'));
        $this->assertEquals(false, $this->varTypeUtility->isBoolean('string'));
    }

    public function testIsInteger()
    {
        $this->assertEquals(true, $this->varTypeUtility->isInteger('int'));
        $this->assertEquals(true, $this->varTypeUtility->isInteger('integer'));
        $this->assertEquals(false, $this->varTypeUtility->isInteger('string'));
    }

    public function testIsFloatingPointNumber()
    {
        $this->assertEquals(true, $this->varTypeUtility->isFloatingPointNumber('float'));
        $this->assertEquals(true, $this->varTypeUtility->isFloatingPointNumber('double'));
        $this->assertEquals(false, $this->varTypeUtility->isFloatingPointNumber('array'));
    }
}
