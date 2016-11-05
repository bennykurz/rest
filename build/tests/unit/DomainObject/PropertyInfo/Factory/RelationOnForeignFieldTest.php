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
use N86io\Rest\DomainObject\PropertyInfo\Factory\RelationOnForeignField;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;

/**
 * Class RelationOnForeignFieldTest
 * @package N86io\Rest\Tests\Unit\DomainObject\PropertyInfo\Factory
 */
class RelationOnForeignFieldTest extends AbstractFactoryTest
{
    /**
     * @var array
     */
    protected $attributes = [
        'type' => 'Entity',
        'foreignField' => 'field'
    ];

    /**
     * @var string
     */
    protected $factoryClass = RelationOnForeignField::class;

    /**
     * @var string
     */
    protected $propertyInfoClass = \N86io\Rest\DomainObject\PropertyInfo\RelationOnForeignField::class;

    public function test()
    {
        parent::test();
        unset($this->attributes['foreignField']);
        $factory = $this->buildFactory();
        $this->assertFalse($factory->check($this->attributes));
    }

    /**
     * @return FactoryInterface
     */
    protected function buildFactory()
    {
        $parentFactory = parent::buildFactory();
        $this->inject($parentFactory, 'propertyInfoUtility', $this->createPropertyInfoUtilityMock());
        return $parentFactory;
    }

    /**
     * @return PropertyInfoUtility
     */
    protected function createPropertyInfoUtilityMock()
    {
        $mock = \Mockery::mock(PropertyInfoUtility::class);
        $mock->shouldReceive('checkForAbstractEntitySubclass')->withAnyArgs()->andReturn(true);
        return $mock;
    }
}
