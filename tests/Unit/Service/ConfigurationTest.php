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
    public function test()
    {
        $controllerMock1Name = get_class(\Mockery::mock(ControllerInterface::class));
        $controllerMock2Name = get_class(\Mockery::mock(ControllerInterface::class));

        /** @var Configuration $configuration */
        $configuration = static::$container->get(Configuration::class);
        $configuration->registerApiModel('api1', FakeEntity1::class, '1');
        $configuration->registerApiModel('api1', FakeEntity2::class, '2');
        $configuration->registerApiModel('api2', FakeEntity3::class, '1');
        $configuration->registerApiModel('api2', FakeEntity4::class, '2');
        $configuration->registerApiController('api1', $controllerMock1Name, '1');
        $configuration->registerApiController('api2', $controllerMock2Name, '2');

        $configuration->setApiBaseUrl('http://example.com/api');
        $this->assertEquals('http://example.com/api', $configuration->getApiBaseUrl());

        $configuration->setApiBaseUrl('http://example.com/api/');
        $this->assertEquals('http://example.com/api', $configuration->getApiBaseUrl());

        $this->assertEquals(['api1', 'api2'], $configuration->getApiIdentifiers());

        $expectedApi1 = [
            '1' => [
                'model' => FakeEntity1::class,
                'controller' => $controllerMock1Name
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
                'controller' => $controllerMock2Name
            ]
        ];
        $expected = [
            'api1' => $expectedApi1,
            'api2' => $expectedApi2
        ];
        $this->assertEquals($expected, $configuration->getApiConfiguration());
        $this->assertEquals($expectedApi1, $configuration->getApiConfiguration('api1'));
        $this->assertEquals($expectedApi2, $configuration->getApiConfiguration('api2'));
    }

    public function testExceptionRegisterApiModel()
    {
        /** @var Configuration $configuration */
        $configuration = static::$container->get(Configuration::class);
        $this->setExpectedException(\InvalidArgumentException::class);
        $configuration->registerApiModel('api1', Configuration::class);
    }

    public function testExceptionRegisterApiController()
    {
        /** @var Configuration $configuration */
        $configuration = static::$container->get(Configuration::class);
        $this->setExpectedException(\InvalidArgumentException::class);
        $configuration->registerApiController('api1', Configuration::class);
    }
}
