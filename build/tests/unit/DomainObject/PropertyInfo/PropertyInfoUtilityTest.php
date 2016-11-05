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
use N86io\Rest\Object\ContainerFactory;
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

    public function testCastValue()
    {
        $this->assertEquals(33, $this->propertyInfoUtility->castValue('int', '33'));
        $this->assertEquals(25, $this->propertyInfoUtility->castValue('integer', '025'));
        $this->assertEquals(1.23, $this->propertyInfoUtility->castValue('float', '1.23'));
        $this->assertEquals(4.12, $this->propertyInfoUtility->castValue('double', '04.12'));
        $this->assertEquals(true, $this->propertyInfoUtility->castValue('bool', '1'));
        $this->assertEquals(false, $this->propertyInfoUtility->castValue('boolean', '0'));
        $timezone = new \DateTimeZone('UTC');
        $expectedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s e', '2016-10-21 17:29:52 UTC');
        /** @var \DateTime $cast */
        $cast = $this->propertyInfoUtility->castValue('DateTime', '1477070992');
        $cast->setTimezone($timezone);
        $this->assertEquals($expectedDateTime, $cast);
        $cast = $this->propertyInfoUtility->castValue('DateTime', '2016-10-21 17:29:52');
        $cast->setTimezone($timezone);
        $this->assertEquals($expectedDateTime, $cast);
        $this->assertEquals('something', $this->propertyInfoUtility->castValue('unknownType', 'something'));
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
        $this->assertFalse($this->propertyInfoUtility->checkForAbstractEntitySubclass(ContainerFactory::class));
    }
}