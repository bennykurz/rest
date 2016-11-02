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
use N86io\Rest\DomainObject\PropertyInfo\Factory\Relation;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\UnitTestCase;

/**
 * Class RelationTest
 * @package N86io\Rest\Tests\DomainObject\PropertyInfo\Factory
 */
class RelationTest extends UnitTestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $attributes1 = [
        'type' => FakeEntity1::class
    ];

    protected $attributes2 = [
        'type' => FakeEntity1::class,
        'foreignField' => 'field'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->factory = static::$container->get(Relation::class);
    }

    public function testBuild()
    {
        $this->assertTrue(
            $this->factory->build('testName', $this->attributes1) instanceof
            \N86io\Rest\DomainObject\PropertyInfo\Relation
        );
    }

    public function testCheck()
    {
        $this->assertTrue($this->factory->check($this->attributes1));
        $this->assertFalse($this->factory->check($this->attributes2));
    }
}
