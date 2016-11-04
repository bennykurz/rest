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

namespace N86io\Rest\Tests\Reflection;

use N86io\Rest\Object\ContainerFactory;
use N86io\Rest\Reflection\EntityClassReflection;
use N86io\Rest\Reflection\MethodNameUtility;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity2;
use N86io\Rest\Tests\DomainObject\FakeEntity3;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityClassReflectionTest
 * @package N86io\Rest\Tests\Reflection
 */
class EntityClassReflectionTest extends UnitTestCase
{
    public function test()
    {
        $fakeEntity1 = new EntityClassReflection(FakeEntity1::class);
        $this->inject($fakeEntity1, 'methodNameUtility', $this->createMethodNameUtilityMock());

        $fakeEntity2 = new EntityClassReflection(FakeEntity2::class);
        $this->inject($fakeEntity2, 'methodNameUtility', $this->createMethodNameUtilityMock());

        $fakeEntity3 = new EntityClassReflection(FakeEntity3::class);
        $this->inject($fakeEntity3, 'methodNameUtility', $this->createMethodNameUtilityMock());

        $this->assertEquals('Class FakeEntity1', $fakeEntity1->getClassSummary());
        $this->assertEquals('Class FakeEntity2', $fakeEntity2->getClassSummary());
        $this->assertEquals('Class FakeEntity1', $fakeEntity3->getClassSummary());

        $this->assertEquals('Some description', $fakeEntity1->getClassDescription());
        $this->assertEquals('Some description', $fakeEntity2->getClassDescription());

        $expected1 = [
            'fakeId' => [
                'type' => 'int'
            ],
            'string' => [
                'type' => 'string',
                'getter' => 'getString'
            ],
            'integer' => [
                'type' => 'int',
                'setter' => 'setInteger'
            ],
            'float' => [
                'type' => 'float'
            ],
            'dateTimeTimestamp' => [
                'type' => '\DateTime',
                'getter' => 'getDateTimeTimestamp',
                'setter' => 'setDateTimeTimestamp'
            ],
            'array' => [
                'type' => 'array',
            ],
            'demoList' => [
                'type' => '\N86io\Rest\Tests\DomainObject\FakeEntity1'
            ],
            'demoList2' => [
                'type' => '\N86io\Rest\Tests\DomainObject\FakeEntity1'
            ],
            'demoList3' => [
                'type' => '\N86io\Rest\Tests\DomainObject\FakeEntity1'
            ],
            'demoList4' => [
                'type' => '\N86io\Rest\Tests\DomainObject\FakeEntity1'
            ]
        ];
        $expected2 = $expected1;
        $expected2['statusPhpDetermination'] = [
            'type' => '__dynamic',
            'getter' => 'getStatusPhpDetermination',
            'setter' => 'setStatusPhpDetermination'
        ];
        $expected2['statusCombined'] = [
            'type' => 'int'
        ];
        $this->assertEquals($expected1, $fakeEntity1->getProperties());
        $this->assertEquals($expected2, $fakeEntity2->getProperties());

        $this->assertEquals([], $fakeEntity1->getParentClasses());
        $this->assertEquals(['N86io\Rest\Tests\DomainObject\FakeEntity1'], $fakeEntity2->getParentClasses());
        $this->assertEquals(['N86io\Rest\Tests\DomainObject\FakeEntity1'], $fakeEntity3->getParentClasses());
    }

    public function testWrongEntityClass()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        /** @var EntityClassReflection $wrongEntityClass */
        static::$container->make(
            EntityClassReflection::class,
            ['className' => ContainerFactory::class]
        );
    }

    /**
     * @return MethodNameUtility
     */
    protected function createMethodNameUtilityMock()
    {
        $mock = \Mockery::mock(MethodNameUtility::class);
        $mock->shouldReceive('isGetterOrSetter')->with('/^(is|get|set).*/')->andReturn(true);
        $mock->shouldReceive('isGetterOrSetter')->withAnyArgs()->andReturn(false);

        $mock->shouldReceive('isGetter')->with('/^(is|get).*/')->andReturn(true);
        $mock->shouldReceive('isGetter')->withAnyArgs()->andReturn(false);

        $mock->shouldReceive('isSetter')->with('/^set.*/')->andReturn(true);
        $mock->shouldReceive('isSetter')->withAnyArgs()->andReturn(false);

        $mock->shouldReceive('createPropertyNameFromMethod')->with('/(set|get)String/')->andReturn('string');
        $mock->shouldReceive('createPropertyNameFromMethod')->with('/(set|get)Integer/')->andReturn('integer');

        $mock->shouldReceive('createPropertyNameFromMethod')->with('/(set|get)DateTimeTimestamp/')
            ->andReturn('dateTimeTimestamp');
        $mock->shouldReceive('createPropertyNameFromMethod')->with('/(set|get)StatusPhpDetermination/')
            ->andReturn('statusPhpDetermination');

        return $mock;
    }
}
