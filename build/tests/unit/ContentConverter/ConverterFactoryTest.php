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

namespace N86io\Rest\Tests\Unit\ContentConverter;

use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\JsonConverter;
use N86io\Rest\Object\Container;
use N86io\Rest\UnitTestCase;

/**
 * Class ConverterFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class ConverterFactoryTest extends UnitTestCase
{
    public function test()
    {
        $containerMock = \Mockery::mock(Container::class);
        $containerMock->shouldReceive('get')->with(JsonConverter::class)->andReturn(
            \Mockery::mock(JsonConverter::class)
                ->shouldReceive('getContentType')->andReturn('application/json')->getMock()
        );

        $converterFactory = new ConverterFactory;
        $this->inject($converterFactory, 'container', $containerMock);

        $this->assertTrue($converterFactory->createFromAccept('application/json') instanceof JsonConverter);
        $this->assertTrue($converterFactory->createFromAccept('') instanceof JsonConverter);
    }
}
