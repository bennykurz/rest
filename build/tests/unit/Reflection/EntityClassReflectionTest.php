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

namespace N86io\Rest\Tests\Unit\Reflection;

use N86io\Rest\Examples\Example1;
use N86io\Rest\Examples\Example2;
use N86io\Rest\Examples\Example3;
use N86io\Rest\Object\ContainerFactory;
use N86io\Rest\Reflection\EntityClassReflection;
use N86io\Rest\Reflection\MethodNameUtility;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityClassReflectionTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityClassReflectionTest extends UnitTestCase
{
    public function test()
    {
        $example1 = new EntityClassReflection(Example1::class);
        $this->inject($example1, 'methodNameUtility', $this->createMethodNameUtilityMock());

        $example2 = new EntityClassReflection(Example2::class);
        $this->inject($example2, 'methodNameUtility', $this->createMethodNameUtilityMock());

        $example3 = new EntityClassReflection(Example3::class);
        $this->inject($example3, 'methodNameUtility', $this->createMethodNameUtilityMock());

        $this->assertEquals('Class Example1', $example1->getClassSummary());
        $this->assertEquals('Class Example2', $example2->getClassSummary());
        $this->assertEquals('Class Example1', $example3->getClassSummary());

        $this->assertEquals('Some description', $example1->getClassDescription());
        $this->assertEquals('Some description', $example2->getClassDescription());

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
                'type' => '\N86io\Rest\Examples\Example1'
            ],
            'demoList2' => [
                'type' => '\N86io\Rest\Examples\Example1'
            ],
            'demoList3' => [
                'type' => '\N86io\Rest\Examples\Example1'
            ],
            'demoList4' => [
                'type' => '\N86io\Rest\Examples\Example1'
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
        $this->assertEquals($expected1, $example1->getProperties());
        $this->assertEquals($expected2, $example2->getProperties());

        $this->assertEquals([], $example1->getParentClasses());
        $this->assertEquals(['N86io\Rest\Examples\Example1'], $example2->getParentClasses());
        $this->assertEquals(['N86io\Rest\Examples\Example1'], $example3->getParentClasses());
    }

    public function testWrongEntityClass()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new EntityClassReflection(ContainerFactory::class);
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
