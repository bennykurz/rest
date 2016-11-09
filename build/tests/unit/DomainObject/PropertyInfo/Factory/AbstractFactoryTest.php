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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo\Factory;

use N86io\Rest\DomainObject\PropertyInfo\Factory\FactoryInterface;
use N86io\Rest\Object\Container;
use N86io\Rest\UnitTestCase;

/**
 * Class AbstractFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
abstract class AbstractFactoryTest extends UnitTestCase
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var string
     */
    protected $factoryClass;

    /**
     * @var string
     */
    protected $propertyInfoClass;

    public function test()
    {
        $factory = $this->buildFactory();
        $this->assertTrue(is_a($factory->build('_name_', $this->attributes), $this->propertyInfoClass));
        $this->assertTrue($factory->check($this->attributes));
    }

    /**
     * @return FactoryInterface
     */
    protected function buildFactory()
    {
        $args = [
            $this->propertyInfoClass,
            [
                '_name_',
                $this->attributes
            ]
        ];

        $containerMock = \Mockery::mock(Container::class);
        $containerMock->shouldReceive('get')
            ->withArgs($args)
            ->andReturn(\Mockery::mock($this->propertyInfoClass));

        $factory = new $this->factoryClass;
        $this->inject($factory, 'container', $containerMock);

        return $factory;
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
