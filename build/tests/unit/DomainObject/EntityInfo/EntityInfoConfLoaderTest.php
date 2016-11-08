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

namespace N86io\Rest\Tests\Unit\DomainObject\EntityInfo;

use N86io\Rest\DomainObject\EntityInfo\EntityInfoConfLoader;
use N86io\Rest\Service\Configuration;
use N86io\Rest\UnitTestCase;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EntityInfoConfLoaderTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoConfLoaderTest extends UnitTestCase
{
    public function testLoad()
    {
        $streamDirectory = vfsStream::setup('entityinfoconfloader');
        $content = json_encode([
            'Model1' => [
                'mode' => ['read'],
                'enableFields' => [
                    'deleted' => 'delete',
                    'disabled' => 'disable',
                    'startTime' => 'startTime',
                    'endTime' => 'endTime'
                ],
                'properties' => [
                    'integer' => ['constraint' => false],
                    'someThing' => ['foreignField' => 'field']
                ]
            ]
        ]);
        $json = vfsStream::newFile('EntityInfoConf.json')
            ->withContent($content)
            ->at($streamDirectory);

        $content = Yaml::dump([
            'Model2' => [
                'table' => 'table_fake',
                'properties' => [
                    'fakeId' => ['resourcePropertyName' => 'uid'],
                    'sql' => ['sqlExpression' => 'sql'],
                    'someThing' => ['resourcePropertyName' => 'something_else']
                ]
            ],
            'Model3' => [
                'properties' => [
                    'nothing' => ['ordering' => true]
                ]
            ]
        ]);
        $yaml = vfsStream::newFile('EntityInfoConf.yaml')
            ->withContent($content)
            ->at($streamDirectory);

        $entityInfoConfReturn = [
            [
                'type' => Configuration::ENTITY_INFO_CONF_FILE | Configuration::ENTITY_INFO_CONF_JSON,
                'content' => $json->url()
            ],
            [
                'type' => Configuration::ENTITY_INFO_CONF_FILE | Configuration::ENTITY_INFO_CONF_YAML,
                'content' => $yaml->url()
            ]
        ];

        $configurationMock = \Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getEntityInfoConfiguration')->andReturn($entityInfoConfReturn);

        $loader = new EntityInfoConfLoader;
        $this->inject($loader, 'configuration', $configurationMock);

        $expected = [
            'Model1' => [
                'mode' => ['read'],
                'enableFields' => [
                    'deleted' => 'delete',
                    'disabled' => 'disable',
                    'startTime' => 'startTime',
                    'endTime' => 'endTime'
                ],
                'properties' => [
                    'integer' => ['constraint' => false],
                    'someThing' => ['foreignField' => 'field']
                ]
            ],
            'Model2' => [
                'table' => 'table_fake',
                'properties' => [
                    'fakeId' => ['resourcePropertyName' => 'uid'],
                    'sql' => ['sqlExpression' => 'sql'],
                    'someThing' => ['resourcePropertyName' => 'something_else']
                ]
            ],
            'Model3' => [
                'properties' => [
                    'nothing' => ['ordering' => true]
                ]
            ]
        ];
        $this->assertEquals($expected, $loader->loadAll());

        $content = '<?php
return [
    \'Model1\' => [
        \'mode\' => [\'write\'],
        \'properties\' => [
            \'integer\' => [\'constraint\' => true]
        ]
    ],
    \'Model2\' => [
        \'table\' => \'table_fake_2\',
        \'properties\' => [
            \'fakeId\' => [\'resourcePropertyName\' => \'fake_id\']
        ]
    ]
];';
        $array = vfsStream::newFile('EntityInfoConf.php')
            ->withContent($content)
            ->at($streamDirectory);

        $entityInfoConfReturn[] = [
            'type' => Configuration::ENTITY_INFO_CONF_FILE | Configuration::ENTITY_INFO_CONF_ARRAY,
            'content' => $array->url()
        ];

        $configurationMock = \Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getEntityInfoConfiguration')->andReturn($entityInfoConfReturn);

        $loader = new EntityInfoConfLoader;
        $this->inject($loader, 'configuration', $configurationMock);

        $expected['Model1']['mode'] = ['write'];
        $expected['Model1']['properties']['integer']['constraint'] = true;
        $expected['Model2']['table'] = 'table_fake_2';
        $expected['Model2']['properties']['fakeId']['resourcePropertyName'] = 'fake_id';

        $this->assertEquals($expected, $loader->loadAll());

        $expected = [
            'mode' => ['write'],
            'table' => 'table_fake_2',
            'enableFields' => [
                'deleted' => 'delete',
                'disabled' => 'disable',
                'startTime' => 'startTime',
                'endTime' => 'endTime'
            ],
            'properties' => [
                'fakeId' => ['resourcePropertyName' => 'fake_id'],
                'integer' => ['constraint' => true],
                'someThing' => ['foreignField' => 'field', 'resourcePropertyName' => 'something_else'],
                'sql' => ['sqlExpression' => 'sql'],
                'nothing' => ['ordering' => true]
            ]
        ];
        $this->assertEquals($expected, $loader->loadSingle('Model3', ['Model1', 'Model2']));
    }

    public function testInvalidJsonException()
    {
        $this->runException(Configuration::ENTITY_INFO_CONF_JSON);
    }

    public function testInvalidYamlException()
    {
        $this->runException(Configuration::ENTITY_INFO_CONF_YAML);
    }

    /**
     * @param int $type
     */
    protected function runException($type)
    {
        $entityInfoConfReturn = [['type' => $type, 'content' => '']];

        $configurationMock = \Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getEntityInfoConfiguration')->andReturn($entityInfoConfReturn);

        $loader = new EntityInfoConfLoader;
        $this->inject($loader, 'configuration', $configurationMock);

        $this->setExpectedException(\Exception::class);
        $loader->loadAll();
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
