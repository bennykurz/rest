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

namespace N86io\Rest\Tests\DomainObject\PropertyInfo;

use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\Relation;
use N86io\Rest\RestObjectManager;
use N86io\Rest\Tests\DomainObject\FakeEntity1;

/**
 * Class PropertyInfoFactoryTest
 * @package N86io\Rest\Tests\DomainObject\PropertyInfo
 */
class PropertyInfoFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyInfoFactory
     */
    protected $propertyInfoFactory;

    public function setUp()
    {
        $this->propertyInfoFactory = (new RestObjectManager)->get(PropertyInfoFactory::class);
    }

    /**
     * @dataProvider buildPropertyInfoDataProvider
     * @param $expectedClassName
     * @param $data
     */
    public function testBuildPropertyInfo($expectedClassName, $data)
    {
        $this->assertEquals(
            $expectedClassName,
            get_class($this->propertyInfoFactory->buildPropertyInfo($data['name'], $data['attributes']))
        );
    }

    public function testBuildUidPropertyInfo()
    {
        $this->assertEquals(
            Common::class,
            get_class($this->propertyInfoFactory->buildUidPropertyInfo(true))
        );
    }

    public function testBuildLanguageFieldPropertyInfo()
    {
        $this->assertEquals(
            Common::class,
            get_class($this->propertyInfoFactory->buildLanguageFieldPropertyInfo('language_uid_field'))
        );
    }

    /**
     * @return array
     */
    public function buildPropertyInfoDataProvider()
    {
        return [
            [
                Relation::class,
                [
                    'name' => 'somename',
                    'attributes' => [
                        'type' => FakeEntity1::class
                    ]
                ]
            ],
            [
                Common::class,
                [
                    'name' => 'somename2',
                    'attributes' => [
                        'type' => 'string'
                    ]
                ]
            ]
        ];
    }
}
