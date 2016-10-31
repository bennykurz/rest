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

namespace N86io\Rest\Tests\DomainObject\EntityInfo;

use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoFactory;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\ObjectContainer;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity2;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityInfoTest
 * @package N86io\Rest\Tests\DomainObject\EntityInfo
 */
class EntityInfoTest extends UnitTestCase
{
    /**
     * @var EntityInfo
     */
    protected $entityInfo1;

    /**
     * @var EntityInfo
     */
    protected $entityInfo2;

    /**
     * @var EntityInfo
     */
    protected $entityInfo3;

    /**
     * @var array
     */
    protected $createdPropertyInfo;

    public function setUp()
    {
        parent::setUp();
        $this->entityInfo1 = new EntityInfo([
            'className' => FakeEntity1::class,
            'storage' => 'storageName',
            'mode' => ['read', 'write'],
            'table' => 'table_name'
        ]);
        $this->entityInfo2 = new EntityInfo([
            'className' => FakeEntity2::class,
            'storage' => 'storageName',
            'mode' => ['read']
        ]);
        $this->entityInfo3 = new EntityInfo([
            'className' => FakeEntity1::class,
            'storage' => 'storageName',
            'mode' => ['write']
        ]);
        $propertyAttributes = [
            [
                'name' => 'fakeId',
                'attributes' => [
                    'type' => 'int',
                    'resourcePropertyName' => 'uid'
                ]
            ],
            [
                'name' => 'someNameFour',
                'attributes' => [
                    'type' => 'int',
                    'outputLevel' => 0,
                    'position' => 3,
                    'resourceId' => true
                ]
            ],
            [
                'name' => 'someNameOne',
                'attributes' => [
                    'type' => FakeEntity1::class,
                    'outputLevel' => 0,
                    'position' => 2
                ]
            ],
            [
                'name' => 'someNameTwo',
                'attributes' => [
                    'type' => 'string',
                    'outputLevel' => 1,
                    'position' => 1
                ]
            ],
            [
                'name' => 'someDynamic',
                'attributes' => [
                    'type' => '__dynamic',
                    'outputLevel' => 5
                ]
            ]
        ];
        /** @var PropertyInfoFactory $propInfoFact */
        $propInfoFact = ObjectContainer::get(PropertyInfoFactory::class);
        foreach ($propertyAttributes as $attribute) {
            $propInfo = $propInfoFact->buildPropertyInfo($attribute['name'], $attribute['attributes']);
            $this->entityInfo1->addPropertyInfo($propInfo);
            $this->createdPropertyInfo[$propInfo->getName()] = $propInfo;
        }
    }

    public function testConstructorException()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new EntityInfo(['className' => EntityInfoTest::class]);
    }

    public function testAddPropertyInfoException1()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->entityInfo1->addPropertyInfo(new Common(
            'someFurtherUid',
            [
                'type' => 'int',
                'resourcePropertyName' => 'uid'
            ]
        ));
    }

    public function testAddPropertyInfoException2()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->entityInfo1->addPropertyInfo(new Common(
            'someFurtherUid',
            [
                'type' => 'int',
                'resourceId' => true
            ]
        ));
    }

    public function test()
    {
        $this->assertEquals('storageName', $this->entityInfo1->getStorage());
        $this->assertEquals(FakeEntity1::class, $this->entityInfo1->getClassName());
        $this->assertEquals('table_name', $this->entityInfo1->getTable());
        $this->assertEquals(
            $this->createdPropertyInfo['someNameFour'],
            $this->entityInfo1->getResourceIdPropertyInfo()
        );
        $this->assertEquals($this->createdPropertyInfo['fakeId'], $this->entityInfo1->getUidPropertyInfo());
        $this->assertEquals(
            $this->createdPropertyInfo['someNameOne'],
            $this->entityInfo1->getPropertyInfo('someNameOne')
        );
        $this->assertEquals(
            $this->createdPropertyInfo['someDynamic'],
            $this->entityInfo1->getPropertyInfo('someDynamic')
        );
        $this->assertEquals($this->createdPropertyInfo, $this->entityInfo1->getPropertyInfoList());
        $this->assertTrue($this->entityInfo1->hasPropertyInfo('someNameOne'));
        $this->assertTrue($this->entityInfo1->hasPropertyInfo('someNameTwo'));
        $this->assertFalse($this->entityInfo1->hasPropertyInfo('someNameTwenty'));
        $this->assertTrue($this->entityInfo1->hasResourceId());
        $this->assertTrue($this->entityInfo1->hasUidPropertyInfo());
        $this->assertEquals('someNameOne', $this->entityInfo1->mapResourcePropertyName('some_name_one'));
        $this->assertEquals('someNameTwo', $this->entityInfo1->mapResourcePropertyName('some_name_two'));
        $this->assertEquals('someNameTwo', $this->entityInfo1->mapResourcePropertyName('someNameTwo'));
        $this->assertEquals('', $this->entityInfo1->mapResourcePropertyName('someNameThree'));

        /**
         * @var PropertyInfoInterface $propInfo1
         * @var PropertyInfoInterface $propInfo2
         * @var PropertyInfoInterface $propInfo3
         */
        $result = $this->entityInfo1->getVisiblePropertiesOrdered(0);
        list($propInfo1, $propInfo2, $propInfo3) = $result;
        $this->assertEquals('fakeId', $propInfo1->getName());
        $this->assertEquals('someNameOne', $propInfo2->getName());
        $this->assertEquals('someNameFour', $propInfo3->getName());
        $this->assertFalse(array_key_exists(3, $result));

        /**
         * @var PropertyInfoInterface $propInfo1
         * @var PropertyInfoInterface $propInfo2
         * @var PropertyInfoInterface $propInfo3
         * @var PropertyInfoInterface $propInfo4
         */
        list($propInfo1, $propInfo2, $propInfo3, $propInfo4) = $this->entityInfo1->getVisiblePropertiesOrdered(1);
        $this->assertEquals('fakeId', $propInfo1->getName());
        $this->assertEquals('someNameTwo', $propInfo2->getName());
        $this->assertEquals('someNameOne', $propInfo3->getName());
        $this->assertEquals('someNameFour', $propInfo4->getName());

        $this->assertTrue($this->entityInfo1->canHandleRequestMode(RequestInterface::REQUEST_MODE_READ));
        $this->assertTrue($this->entityInfo1->canHandleRequestMode(RequestInterface::REQUEST_MODE_CREATE));
        $this->assertTrue($this->entityInfo1->canHandleRequestMode(RequestInterface::REQUEST_MODE_DELETE));
        $this->assertTrue($this->entityInfo1->canHandleRequestMode(RequestInterface::REQUEST_MODE_UPDATE));
        $this->assertTrue($this->entityInfo1->canHandleRequestMode(RequestInterface::REQUEST_MODE_PATCH));

        $this->assertTrue($this->entityInfo2->canHandleRequestMode(RequestInterface::REQUEST_MODE_READ));
        $this->assertFalse($this->entityInfo2->canHandleRequestMode(RequestInterface::REQUEST_MODE_CREATE));
        $this->assertFalse($this->entityInfo2->canHandleRequestMode(RequestInterface::REQUEST_MODE_DELETE));
        $this->assertFalse($this->entityInfo2->canHandleRequestMode(RequestInterface::REQUEST_MODE_UPDATE));
        $this->assertFalse($this->entityInfo2->canHandleRequestMode(RequestInterface::REQUEST_MODE_PATCH));

        $this->assertFalse($this->entityInfo3->canHandleRequestMode(RequestInterface::REQUEST_MODE_READ));
        $this->assertTrue($this->entityInfo3->canHandleRequestMode(RequestInterface::REQUEST_MODE_CREATE));
        $this->assertFalse($this->entityInfo3->canHandleRequestMode(RequestInterface::REQUEST_MODE_DELETE));
        $this->assertFalse($this->entityInfo3->canHandleRequestMode(RequestInterface::REQUEST_MODE_UPDATE));
        $this->assertFalse($this->entityInfo3->canHandleRequestMode(RequestInterface::REQUEST_MODE_PATCH));
    }
}
