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

use Mockery\MockInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\AbstractPropertyInfo;
use N86io\Rest\UnitTestCase;

/**
 * Class AbstractPropertyInfoTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class AbstractPropertyInfoTest extends UnitTestCase
{
    public function test()
    {
        $attributes = [
            'hide' => false,
            'position' => 3,
            'outputLevel' => 2,
            'getter' => 'getTest'
        ];
        $attributesInt = array_merge($attributes, ['type' => 'int']);

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractPropertyInfo $abstrPropertyInfoInt */
        $abstrPropertyInfoInt = $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', $attributesInt]);
        $this->inject($abstrPropertyInfoInt, 'entityInfoStorage', $this->createEntityInfoStorageMock());


        $this->assertEquals('test', $abstrPropertyInfoInt->getName());
        $this->assertEquals('int', $abstrPropertyInfoInt->getType());
        $this->assertEquals(3, $abstrPropertyInfoInt->getPosition());
        $this->assertEquals('getTest', $abstrPropertyInfoInt->getGetter());
        $this->assertEquals('', $abstrPropertyInfoInt->getSetter());
        $this->assertTrue($abstrPropertyInfoInt->shouldShow(2));
        $this->assertTrue($abstrPropertyInfoInt->shouldShow(3));
        $this->assertFalse($abstrPropertyInfoInt->shouldShow(1));
        $this->assertFalse($abstrPropertyInfoInt->shouldShow(0));
        $this->assertFalse($abstrPropertyInfoInt->shouldShow(1));
        $this->assertTrue($abstrPropertyInfoInt->getEntityInfo() instanceof EntityInfo);


        $isInteger = false;
        /** @var MockInterface|EntityInterface $entity */
        $entity = \Mockery::mock(EntityInterface::class)
            ->shouldReceive('getProperty')->with('test')->andReturn('10')->getMock()
            ->shouldReceive('setProperty')->withAnyArgs()->andReturnUsing(
                function ($_, $value) use (&$isInteger) {
                    $isInteger = is_integer($value);
                }
            )->getMock();
        $abstrPropertyInfoInt->castValue($entity);
        $this->assertTrue($isInteger);


        $isFloat = false;
        $attributesFloat = array_merge($attributes, ['type' => 'float']);
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractPropertyInfo $abstrPropertyInfoFloat */
        $abstrPropertyInfoFloat = $this->getMockForAbstractClass(
            AbstractPropertyInfo::class,
            ['test', $attributesFloat]
        );
        /** @var MockInterface|EntityInterface $entity */
        $entity = \Mockery::mock(EntityInterface::class)
            ->shouldReceive('getProperty')->with('test')->andReturn('10')->getMock()
            ->shouldReceive('setProperty')->withAnyArgs()->andReturnUsing(
                function ($_, $value) use (&$isFloat) {
                    $isFloat = is_float($value);
                }
            )->getMock();
        $abstrPropertyInfoFloat->castValue($entity);
        $this->assertTrue($isFloat);


        $isBoolean = false;
        $attributesBool = array_merge($attributes, ['type' => 'bool']);
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractPropertyInfo $abstrPropertyInfoBool */
        $abstrPropertyInfoBool = $this->getMockForAbstractClass(
            AbstractPropertyInfo::class,
            ['test', $attributesBool]
        );
        /** @var MockInterface|EntityInterface $entity */
        $entity = \Mockery::mock(EntityInterface::class)
            ->shouldReceive('getProperty')->with('test')->andReturn('true')->getMock()
            ->shouldReceive('setProperty')->withAnyArgs()->andReturnUsing(
                function ($_, $value) use (&$isBoolean) {
                    $isBoolean = is_bool($value);
                }
            )->getMock();
        $abstrPropertyInfoBool->castValue($entity);
        $this->assertTrue($isBoolean);


        $isDateTime = false;
        $attributesDate = array_merge($attributes, ['type' => 'DateTime']);
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractPropertyInfo $abstrPropertyInfoDate */
        $abstrPropertyInfoDate = $this->getMockForAbstractClass(
            AbstractPropertyInfo::class,
            ['test', $attributesDate]
        );
        /** @var MockInterface|EntityInterface $entity */
        $entity = \Mockery::mock(EntityInterface::class)
            ->shouldReceive('getProperty')->with('test')->andReturn('2016-11-16 10:30:39')->getMock()
            ->shouldReceive('setProperty')->withAnyArgs()->andReturnUsing(
                function ($_, \DateTime $dateTime) use (&$isDateTime) {
                    $isDateTime = $dateTime->format('Y-m-d H:i:s') === '2016-11-16 10:30:39';
                }
            )->getMock();
        $abstrPropertyInfoDate->castValue($entity);
        $this->assertTrue($isDateTime);

        $isDateTime = false;
        /** @var MockInterface|EntityInterface $entity */
        $entity = \Mockery::mock(EntityInterface::class)
            ->shouldReceive('getProperty')->with('test')->andReturn(123456)->getMock()
            ->shouldReceive('setProperty')->withAnyArgs()->andReturnUsing(
                function ($_, \DateTime $dateTime) use (&$isDateTime) {
                    $isDateTime = $dateTime->getTimestamp() === 123456;
                }
            )->getMock();
        $abstrPropertyInfoDate->castValue($entity);
        $this->assertTrue($isDateTime);
    }

    public function testConstructor()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(AbstractPropertyInfo::class, ['test', []]);
    }

    /**
     * @return MockInterface|EntityInfoStorage
     */
    protected function createEntityInfoStorageMock()
    {
        return \Mockery::mock(EntityInfoStorage::class)
            ->shouldReceive('get')->withAnyArgs()->andReturn(
                \Mockery::mock(EntityInfo::class)
            )->getMock();
    }
}
