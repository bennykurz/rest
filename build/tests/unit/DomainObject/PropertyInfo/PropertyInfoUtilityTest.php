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

use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoUtility;
use N86io\Rest\Object\Container;
use N86io\Rest\UnitTestCase;

/**
 * Class PropertyInfoUtilityTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class PropertyInfoUtilityTest extends UnitTestCase
{
    /**
     * @var PropertyInfoUtility
     */
    protected $propertyInfoUtility;

    public function setUp()
    {
        parent::setUp();
        $this->propertyInfoUtility = new PropertyInfoUtility;
    }

    public function testPlaceTableAlias()
    {
        $this->assertEquals('a.test', $this->propertyInfoUtility->placeTableAlias('%test%', 'a'));
        $this->assertEquals(
            'somethinga.testsomething',
            $this->propertyInfoUtility->placeTableAlias('something%test%something', 'a')
        );
        $this->assertEquals(
            'a.test',
            $this->propertyInfoUtility->placeTableAlias('%aliasPlaceholder%test%', 'a', 'aliasPlaceholder')
        );
    }

    public function testCheckForAbstractEntitySubclass()
    {
        $entityMock = get_class(\Mockery::mock(AbstractEntity::class));
        $this->assertTrue($this->propertyInfoUtility->checkForAbstractEntitySubclass($entityMock));
        $this->assertFalse($this->propertyInfoUtility->checkForAbstractEntitySubclass(Container::class));
    }
}
