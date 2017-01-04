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

namespace N86io\Rest\Tests\Unit\Service;

use N86io\Rest\ControllerInterface;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\Service\Configuration;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class ConfigurationTest extends UnitTestCase
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $controllerMock1Name;

    /**
     * @var string
     */
    protected $controllerMock2Name;

    /**
     * @var array
     */
    protected $entityClassNames;

    public function setUp()
    {
        $this->controllerMock1Name = get_class(\Mockery::mock(ControllerInterface::class));
        $this->controllerMock2Name = get_class(\Mockery::mock(ControllerInterface::class));

        $this->entityClassNames = [
            1 => get_class(\Mockery::mock(EntityInterface::class)),
            2 => get_class(\Mockery::mock(EntityInterface::class)),
            3 => get_class(\Mockery::mock(EntityInterface::class)),
            4 => get_class(\Mockery::mock(EntityInterface::class))
        ];

        $this->configuration = new Configuration;
        $this->configuration->registerApiModel('api1', $this->entityClassNames[1], '1');
        $this->configuration->registerApiModel('api1', $this->entityClassNames[2], '2');
        $this->configuration->registerApiModel('api2', $this->entityClassNames[3], '1');
        $this->configuration->registerApiModel('api2', $this->entityClassNames[4], '2');
        $this->configuration->registerApiController('api1', $this->controllerMock1Name, '1');
        $this->configuration->registerApiController('api2', $this->controllerMock2Name, '2');
        $this->configuration->registerAlias('aliasForApi1', 'api1');
    }

    public function test()
    {
        $secondConfiguration = new Configuration;
        $secondConfiguration->overrideConfiguration($this->configuration);

        $this->assertEquals($this->configuration, $secondConfiguration);

        $this->configuration->setApiBaseUrl('http://example.com/api');
        $this->assertEquals('http://example.com/api', $this->configuration->getApiBaseUrl());

        $this->configuration->setApiBaseUrl('http://example.com/api/');
        $this->assertEquals('http://example.com/api', $this->configuration->getApiBaseUrl());

        $this->assertEquals(['api1', 'api2', 'aliasForApi1'], $this->configuration->getApiIdentifiers());

        $expectedApi1 = [
            '1' => [
                'model'      => $this->entityClassNames[1],
                'controller' => $this->controllerMock1Name
            ],
            '2' => [
                'model' => $this->entityClassNames[2]
            ]
        ];
        $expectedApi2 = [
            '1' => [
                'model' => $this->entityClassNames[3]
            ],
            '2' => [
                'model'      => $this->entityClassNames[4],
                'controller' => $this->controllerMock2Name
            ]
        ];
        $this->assertEquals($expectedApi1, $this->configuration->getApiConfiguration('api1'));
        $this->assertEquals($expectedApi1, $this->configuration->getApiConfiguration('aliasForApi1'));
        $this->assertEquals($expectedApi2, $this->configuration->getApiConfiguration('api2'));

        $settings1 = ['settings1'];
        $settings2 = ['settings2'];
        $settings3 = ['settings3'];
        $this->configuration->registerApiControllerSettings('api1', $settings1);
        $this->configuration->registerApiControllerSettings('api2', $settings2);
        $this->assertEquals($settings1, $this->configuration->getApiControllerSettings('api1'));
        $this->assertEquals($settings2, $this->configuration->getApiControllerSettings('api2'));
        $this->assertEquals([], $this->configuration->getApiControllerSettings('aliasForApi1'));
        $this->configuration->registerApiControllerSettings('aliasForApi1', $settings3);
        $this->assertEquals($settings3, $this->configuration->getApiControllerSettings('aliasForApi1'));


        $clearConf = new Configuration;
        $this->configuration->overrideConfiguration($clearConf);
        $this->configuration->registerEntityInfoConfiguration('SomeContent');
        $this->configuration->registerEntityInfoConfiguration([]);
        $this->configuration->registerEntityInfoConfiguration(__FILE__);
        $expected = [
            [
                'type'    => Configuration::ENTITY_INFO_CONF_JSON,
                'content' => 'SomeContent'
            ],
            [
                'type'    => Configuration::ENTITY_INFO_CONF_ARRAY,
                'content' => []
            ],
            [
                'type'    => Configuration::ENTITY_INFO_CONF_JSON_FILE,
                'content' => __FILE__
            ]
        ];
        $this->assertEquals($expected, $this->configuration->getEntityInfoConfiguration());
    }

    public function testExceptionRegisterApiModel()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->registerApiModel('api1', Configuration::class);
    }

    public function testExceptionRegisterApiController()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->registerApiController('api1', Configuration::class);
    }

    public function testExceptionRegisterAliasOnAlias()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->registerAlias('otherAlias', 'aliasForApi1');
    }

    public function testExceptionRegisterAliasExistedApiIdentifier()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->registerAlias('api1', 'api2');
    }

    public function testExceptionRegisterAliasOnUnknownApiIdentifier()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->registerAlias('aliasForUnknown', 'Unknown');
    }

    public function testExceptionRegisterModelOnAlias()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->configuration->registerApiModel('aliasForApi1', 'Irrelevant');
    }
}
