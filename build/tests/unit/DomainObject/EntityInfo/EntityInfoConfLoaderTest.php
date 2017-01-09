<?php declare(strict_types = 1);
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

use Doctrine\Instantiator\Instantiator;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoConfLoader;
use N86io\Rest\Service\Configuration;
use N86io\Rest\UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoConfLoaderTest extends UnitTestCase
{
    private $conf1 = [
        'Model1' => [
            'table'        => 'model1_table',
            'mode'         => ['read'],
            'enableFields' => [
                'deleted'   => 'delete',
                'disabled'  => 'disable',
                'startTime' => 'startTime',
                'endTime'   => 'endTime'
            ],
            'properties'   => [
                'integer' => ['constraint' => false]
            ]
        ]
    ];

    private $conf2 = [
        'Model2' => [
            'table'      => 'table_fake',
            'properties' => [
                'fakeId' => ['resourcePropertyName' => 'uid']
            ]
        ],
        'Model3' => [
            'table'      => 'model3_table',
            'properties' => [
                'nothing' => ['ordering' => true]
            ]
        ]
    ];

    public function testLoadAll()
    {
        $expected['Model1'] = $this->conf1['Model1'];
        $expected['Model1']['properties'] = [
            'integer' => [
                'constraint'           => false,
                'resourceId'           => false,
                'hide'                 => false,
                'outputLevel'          => 0,
                'position'             => 0,
                'ordering'             => false,
                'uid'                  => false,
                'resourcePropertyName' => \N86io\ArrayConf\Configuration::EMPTY
            ]
        ];
        $expected['Model1']['connector'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model1']['joins'] = \N86io\ArrayConf\Configuration::EMPTY;

        $expected['Model2'] = $this->conf2['Model2'];
        $expected['Model2']['properties'] = [
            'fakeId' => [
                'resourcePropertyName' => 'uid',
                'resourceId'           => false,
                'hide'                 => false,
                'outputLevel'          => 0,
                'position'             => 0,
                'ordering'             => false,
                'constraint'           => false,
                'uid'                  => false
            ]
        ];
        $expected['Model2']['connector'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model2']['joins'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model2']['mode'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model2']['enableFields'] = \N86io\ArrayConf\Configuration::EMPTY;

        $expected['Model3'] = $this->conf2['Model3'];
        $expected['Model3']['properties'] = [
            'nothing' => [
                'ordering'             => true,
                'resourceId'           => false,
                'hide'                 => false,
                'outputLevel'          => 0,
                'position'             => 0,
                'constraint'           => false,
                'uid'                  => false,
                'resourcePropertyName' => \N86io\ArrayConf\Configuration::EMPTY
            ]
        ];
        $expected['Model3']['connector'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model3']['joins'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model3']['mode'] = \N86io\ArrayConf\Configuration::EMPTY;
        $expected['Model3']['enableFields'] = \N86io\ArrayConf\Configuration::EMPTY;

        $this->assertEquals($expected, $this->getLoader()->loadAll());
    }

    public function testLoadSingle()
    {
        $expected = [
            'table'        => 'model1_table',
            'mode'         => ['read'],
            'enableFields' => [
                'deleted'   => 'delete',
                'disabled'  => 'disable',
                'startTime' => 'startTime',
                'endTime'   => 'endTime'
            ],
            'connector'    => \N86io\ArrayConf\Configuration::EMPTY,
            'joins'        => \N86io\ArrayConf\Configuration::EMPTY,
            'properties'   => [
                'fakeId'  => [
                    'resourcePropertyName' => 'uid',
                    'resourceId'           => false,
                    'hide'                 => false,
                    'outputLevel'          => 0,
                    'position'             => 0,
                    'ordering'             => false,
                    'constraint'           => false,
                    'uid'                  => false
                ],
                'nothing' => [
                    'ordering'             => true,
                    'resourceId'           => false,
                    'hide'                 => false,
                    'outputLevel'          => 0,
                    'position'             => 0,
                    'constraint'           => false,
                    'uid'                  => false,
                    'resourcePropertyName' => \N86io\ArrayConf\Configuration::EMPTY
                ],
                'integer' => [
                    'constraint'           => false,
                    'resourceId'           => false,
                    'hide'                 => false,
                    'outputLevel'          => 0,
                    'position'             => 0,
                    'ordering'             => false,
                    'uid'                  => false,
                    'resourcePropertyName' => \N86io\ArrayConf\Configuration::EMPTY
                ]
            ]
        ];
        $this->assertEquals($expected, $this->getLoader()->loadSingle('Model1', ['Model2', 'Model3']));
    }

    public function testInvalidJson()
    {
        $entityInfoConfReturn = [
            [
                'type'    => Configuration::ENTITY_INFO_CONF_JSON,
                'content' => ''
            ]
        ];

        $configurationMock = \Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getEntityInfoConfiguration')->andReturn($entityInfoConfReturn);

        /** @var EntityInfoConfLoader $loader */
        $loader = (new Instantiator)->instantiate(EntityInfoConfLoader::class);
        $this->inject($loader, 'configuration', $configurationMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid JSON.');

        $loader->__construct();
    }

    /**
     * @return EntityInfoConfLoader
     */
    private function getLoader(): EntityInfoConfLoader
    {
        $streamDirectory = vfsStream::setup('entityinfoconfloader');
        $json = vfsStream::newFile('EntityInfoConf.json')
            ->withContent(json_encode($this->conf1))
            ->at($streamDirectory);

        $entityInfoConfReturn = [
            [
                'type'    => Configuration::ENTITY_INFO_CONF_JSON_FILE,
                'content' => $json->url()
            ],
            [
                'type'    => Configuration::ENTITY_INFO_CONF_ARRAY,
                'content' => $this->conf2
            ]
        ];

        $configurationMock = \Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getEntityInfoConfiguration')->andReturn($entityInfoConfReturn);

        /** @var EntityInfoConfLoader $loader */
        $loader = (new Instantiator)->instantiate(EntityInfoConfLoader::class);
        $this->inject($loader, 'configuration', $configurationMock);
        $loader->__construct();

        return $loader;
    }
}
