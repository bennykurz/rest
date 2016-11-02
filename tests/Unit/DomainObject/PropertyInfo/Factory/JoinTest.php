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

namespace N86io\Rest\Tests\DomainObject\PropertyInfo\Factory;

use N86io\Rest\DomainObject\PropertyInfo\Factory\FactoryInterface;
use N86io\Rest\DomainObject\PropertyInfo\Factory\Join;
use N86io\Rest\UnitTestCase;

/**
 * Class JoinTest
 * @package N86io\Rest\Tests\DomainObject\PropertyInfo\Factory
 */
class JoinTest extends UnitTestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $attributes = [
        'type' => 'int',
        'joinTable' => 'table',
        'joinCondition' => 'condition'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->factory = $this->factory = static::$container->get(Join::class);
    }

    public function testBuild()
    {
        $this->assertTrue(
            $this->factory->build('testName', $this->attributes) instanceof
            \N86io\Rest\DomainObject\PropertyInfo\Join
        );
    }

    public function testCheck()
    {
        $this->assertTrue($this->factory->check($this->attributes));
    }
}
