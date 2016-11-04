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

use N86io\Rest\DomainObject\EntityInfo\EntityInfoConfLoader;
use N86io\Rest\Service\Configuration;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity2;
use N86io\Rest\Tests\DomainObject\FakeEntity4;
use N86io\Rest\UnitTestCase;

/**
 * Class EntityInfoConfLoaderTest
 * @package N86io\Rest\Tests\DomainObject\EntityInfo
 */
class EntityInfoConfLoaderTest extends UnitTestCase
{
    public function testLoad()
    {
        /** @var Configuration $configuration */
        $configuration = static::$container->get(Configuration::class);
        $configuration->registerEntityInfoConfiguration(
            __DIR__ . '/../EntityInfoConf2.php',
            Configuration::ENTITY_INFO_CONF_FILE + Configuration::ENTITY_INFO_CONF_ARRAY
        );

        /** @var EntityInfoConfLoader $loader */
        $loader = static::$container->get(EntityInfoConfLoader::class);
        $expected = [
            FakeEntity1::class => [
                'table' => 'table_fake_2',
                'mode' => ['read', 'write'],
                'properties' => [
                    'fakeId' => ['resourcePropertyName' => 'uid', 'resourceId' => true],
                    'string' => ['ordering' => false, 'hide' => true],
                    'integer' => ['constraint' => true],
                    'float' => ['hide' => false],
                    'dateTimeTimestamp' => ['outputLevel' => 6],
                    'array' => ['position' => 2]
                ]
            ],
            FakeEntity2::class => [
                'mode' => ['write'],
                'properties' => [
                    'integer' => ['constraint' => false],
                    'float' => ['hide' => true],
                    'dateTimeTimestamp' => ['outputLevel' => 106],
                    'array' => ['position' => 102],
                    'statusCombined' => [
                        'sqlExpression' => 'CONV(BINARY(CONCAT(%value_a%, %value_b%, %value_c%)),2,10)'
                    ],
                    'statusPhpDetermination' => ['position' => 15, 'outputLevel' => 2]
                ]
            ],
            FakeEntity4::class => [
                'table' => 'table_fake',
                'mode' => ['read'],
                'properties' => [
                    'string' => ['ordering' => true]
                ]
            ]
        ];
        $this->assertEquals($expected, $loader->loadAll());

        $expected = [
            'table' => 'table_fake',
            'mode' => ['read'],
            'properties' => [
                'fakeId' => ['resourcePropertyName' => 'uid', 'resourceId' => true],
                'string' => ['ordering' => true, 'hide' => true],
                'integer' => ['constraint' => false],
                'float' => ['hide' => true],
                'dateTimeTimestamp' => ['outputLevel' => 106],
                'array' => ['position' => 102],
                'statusCombined' => [
                    'sqlExpression' => 'CONV(BINARY(CONCAT(%value_a%, %value_b%, %value_c%)),2,10)'
                ],
                'statusPhpDetermination' => ['position' => 15, 'outputLevel' => 2]
            ]
        ];
        $this->assertEquals(
            $expected,
            $loader->loadSingle(
                FakeEntity4::class,
                [
                    FakeEntity1::class,
                    FakeEntity2::class
                ]
            )
        );
    }
}
