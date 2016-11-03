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
    /**
     * @var EntityClassReflection
     */
    protected $fakeEntity1;

    /**
     * @var EntityClassReflection
     */
    protected $fakeEntity2;

    /**
     * @var EntityClassReflection
     */
    protected $fakeEntity3;

    public function setUp()
    {
        parent::setUp();
        $this->fakeEntity1 = static::$container->make(
            EntityClassReflection::class,
            ['className' => FakeEntity1::class]
        );
        $this->fakeEntity2 = static::$container->make(
            EntityClassReflection::class,
            ['className' => FakeEntity2::class]
        );
        $this->fakeEntity3 = static::$container->make(
            EntityClassReflection::class,
            ['className' => FakeEntity3::class]
        );
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

    public function testGetClassSummary()
    {
        $this->assertEquals('Class FakeEntity1', $this->fakeEntity1->getClassSummary());
        $this->assertEquals('Class FakeEntity2', $this->fakeEntity2->getClassSummary());
        $this->assertEquals('Class FakeEntity1', $this->fakeEntity3->getClassSummary());
    }

    public function testGetClassDescription()
    {
        $this->assertEquals('Some description', $this->fakeEntity1->getClassDescription());
        $this->assertEquals('Some description', $this->fakeEntity2->getClassDescription());
    }

    public function testGetProperties()
    {
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
        $this->assertEquals($expected1, $this->fakeEntity1->getProperties());
        $this->assertEquals($expected2, $this->fakeEntity2->getProperties());
    }
}
