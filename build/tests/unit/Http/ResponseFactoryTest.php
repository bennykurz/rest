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

namespace N86io\Rest\Tests\Unit\Http;

use N86io\Di\Container;
use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\ConverterInterface;
use N86io\Rest\Http\ResponseFactory;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Viktor Firus <v@n86.io>
 */
class ResponseFactoryTest extends UnitTestCase
{
    public function test()
    {
        $serverRequest = \Mockery::mock(ServerRequestInterface::class);
        $serverRequest->shouldReceive('getUri')->andReturn('RequestUrl');
        $serverRequest->shouldReceive('getHeader')->with('accept')->andReturn(['application/json']);

        $responseFactory = new ResponseFactory;
        $this->inject($responseFactory, 'container', $this->createContainerMock());
        $this->inject($responseFactory, 'converterFactory', $this->createConverterFactory());
        $responseFactory->setServerRequest($serverRequest);

        $this->assertTrue($responseFactory->badRequest() instanceof ResponseInterface);
        $this->assertTrue($responseFactory->unauthorized() instanceof ResponseInterface);
        $this->assertTrue($responseFactory->notFound() instanceof ResponseInterface);
        $this->assertTrue($responseFactory->methodNotAllowed() instanceof ResponseInterface);
        $this->assertTrue($responseFactory->internalServerError() instanceof ResponseInterface);
        $this->assertTrue($responseFactory->createResponse(200, []) instanceof ResponseInterface);

        $this->assertTrue($responseFactory->errorCode(400) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorCode(401) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorCode(404) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorCode(405) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorCode(500) instanceof ResponseInterface);
    }

    protected function createConverterFactory()
    {
        $mock = \Mockery::mock(ConverterFactory::class);
        $mock->shouldReceive('createFromAccept')->withAnyArgs()->andReturn($this->createContentConverterMock());

        return $mock;
    }

    protected function createContentConverterMock()
    {
        $mock = \Mockery::mock(ConverterInterface::class);
        $mock->shouldReceive('getContentType')->andReturn('');
        $mock->shouldReceive('render')->withAnyArgs()->andReturn('');

        return $mock;
    }

    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(ResponseInterface::class)->andReturn($this->createResponseMock());

        return $mock;
    }

    protected function createResponseMock()
    {
        $mock = \Mockery::mock(ResponseInterface::class);
        $mock->shouldReceive('withAddedHeader')->withAnyArgs()->andReturnSelf();
        $mock->shouldReceive('withStatus')->withAnyArgs()->andReturnSelf();
        $mock->shouldReceive('getBody')->withAnyArgs()->andReturn(
            \Mockery::mock(StreamInterface::class)
                ->shouldReceive('write')->withAnyArgs()->getMock()
        );

        return $mock;
    }
}
