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

namespace N86io\Rest\Tests\Service;

use N86io\Rest\ControllerInterface;
use N86io\Rest\Service\Configuration;
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity2;
use N86io\Rest\Tests\DomainObject\FakeEntity3;
use N86io\Rest\Tests\DomainObject\FakeEntity4;
use N86io\Rest\UnitTestCase;

/**
 * Class ConfigurationTest
 * @package N86io\Rest\Tests\Service
 */
class ConfigurationTest extends UnitTestCase
{
    /**
     * @var string
     */
    protected $controllerMock1Name;

    /**
     * @var string
     */
    protected $controllerMock2Name;

    public function setUp()
    {
        parent::setUpBeforeClass();

        $this->controllerMock1Name = get_class(\Mockery::mock(ControllerInterface::class));
        $this->controllerMock2Name = get_class(\Mockery::mock(ControllerInterface::class));

        static::$configuration->registerApiModel('api1', FakeEntity1::class, '1');
        static::$configuration->registerApiModel('api1', FakeEntity2::class, '2');
        static::$configuration->registerApiModel('api2', FakeEntity3::class, '1');
        static::$configuration->registerApiModel('api2', FakeEntity4::class, '2');
        static::$configuration->registerApiController('api1', $this->controllerMock1Name, '1');
        static::$configuration->registerApiController('api2', $this->controllerMock2Name, '2');
        static::$configuration->registerAlias('aliasForApi1', 'api1');
    }

    public function test()
    {
        $secondConfiguration = new Configuration;
        $secondConfiguration->injectConfiguration(static::$configuration);

        $this->assertEquals(static::$configuration, $secondConfiguration);

        static::$configuration->setApiBaseUrl('http://example.com/api');
        $this->assertEquals('http://example.com/api', static::$configuration->getApiBaseUrl());

        static::$configuration->setApiBaseUrl('http://example.com/api/');
        $this->assertEquals('http://example.com/api', static::$configuration->getApiBaseUrl());

        $this->assertEquals(['api1', 'api2', 'aliasForApi1'], static::$configuration->getApiIdentifiers());

        $expectedApi1 = [
            '1' => [
                'model' => FakeEntity1::class,
                'controller' => $this->controllerMock1Name
            ],
            '2' => [
                'model' => FakeEntity2::class
            ]
        ];
        $expectedApi2 = [
            '1' => [
                'model' => FakeEntity3::class
            ],
            '2' => [
                'model' => FakeEntity4::class,
                'controller' => $this->controllerMock2Name
            ]
        ];
        $this->assertEquals($expectedApi1, static::$configuration->getApiConfiguration('api1'));
        $this->assertEquals($expectedApi1, static::$configuration->getApiConfiguration('aliasForApi1'));
        $this->assertEquals($expectedApi2, static::$configuration->getApiConfiguration('api2'));

        $settings1 = ['settings1'];
        $settings2 = ['settings2'];
        $settings3 = ['settings3'];
        static::$configuration->registerApiControllerSettings('api1', $settings1);
        static::$configuration->registerApiControllerSettings('api2', $settings2);
        $this->assertEquals($settings1, static::$configuration->getApiControllerSettings('api1'));
        $this->assertEquals($settings2, static::$configuration->getApiControllerSettings('api2'));
        $this->assertEquals([], static::$configuration->getApiControllerSettings('aliasForApi1'));
        static::$configuration->registerApiControllerSettings('aliasForApi1', $settings3);
        $this->assertEquals($settings3, static::$configuration->getApiControllerSettings('aliasForApi1'));


        $clearConf = new Configuration;
        static::$configuration->injectConfiguration($clearConf);
        static::$configuration->registerEntityInfoConfiguration('SomeContent', Configuration::ENTITY_INFO_CONF_ARRAY);
        static::$configuration->registerEntityInfoConfiguration(
            'SomeFurtherContent',
            Configuration::ENTITY_INFO_CONF_JSON + Configuration::ENTITY_INFO_CONF_FILE
        );
        $expected = [
            [
                'type' => Configuration::ENTITY_INFO_CONF_ARRAY,
                'content' => 'SomeContent'
            ],
            [
                'type' => Configuration::ENTITY_INFO_CONF_JSON + Configuration::ENTITY_INFO_CONF_FILE,
                'content' => 'SomeFurtherContent'
            ]
        ];
        $this->assertEquals($expected, static::$configuration->getEntityInfoConfiguration());
    }

    public function testExceptionRegisterApiModel()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerApiModel('api1', Configuration::class);
    }

    public function testExceptionRegisterApiController()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerApiController('api1', Configuration::class);
    }

    public function testExceptionRegisterAliasOnAlias()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerAlias('otherAlias', 'aliasForApi1');
    }

    public function testExceptionRegisterAliasExistedApiIdentifier()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerAlias('api1', 'api2');
    }

    public function testExceptionRegisterAliasOnUnknownApiIdentifier()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerAlias('aliasForUnknown', 'Unknown');
    }

    public function testExceptionRegisterModelOnAlias()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerApiModel('aliasForApi1', 'Irrelevant');
    }

    public function testExceptionInvalidEntityInfoConfType()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        static::$configuration->registerEntityInfoConfiguration(
            'Content',
            Configuration::ENTITY_INFO_CONF_YAML + Configuration::ENTITY_INFO_CONF_ARRAY
        );
    }
}
