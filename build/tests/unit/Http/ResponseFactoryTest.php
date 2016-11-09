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

namespace N86io\Rest\Tests\Unit\Http;

use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\ConverterInterface;
use N86io\Rest\Http\ResponseFactory;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ResponseFactoryTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class ResponseFactoryTest extends UnitTestCase
{
    public function test()
    {
        /** @var ServerRequestInterface $serverRequest */
        $serverRequest = \Mockery::mock(ServerRequestInterface::class);
        $serverRequest->shouldReceive('getUri')->andReturn('RequestUrl');
        $serverRequest->shouldReceive('getHeader')->with('accept')->andReturn('application/json');

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

        $this->assertTrue($responseFactory->errorRequest(400) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorRequest(401) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorRequest(404) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorRequest(405) instanceof ResponseInterface);
        $this->assertTrue($responseFactory->errorRequest(500) instanceof ResponseInterface);
    }

    /**
     * @return ConverterFactory
     */
    protected function createConverterFactory()
    {
        $mock = \Mockery::mock(ConverterFactory::class);
        $mock->shouldReceive('createFromAccept')->withAnyArgs()->andReturn($this->createContentConverterMock());
        return $mock;
    }

    /**
     * @return ConverterInterface
     */
    protected function createContentConverterMock()
    {
        $mock = \Mockery::mock(ConverterInterface::class);
        $mock->shouldReceive('getContentType')->andReturn('');
        $mock->shouldReceive('render')->withAnyArgs()->andReturn('');
        return $mock;
    }

    /**
     * @return Container
     */
    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->with(ResponseInterface::class)->andReturn($this->createResponseMock());
        return $mock;
    }

    /**
     * @return ResponseInterface
     */
    protected function createResponseMock()
    {
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('write')->withAnyArgs();

        $mock = \Mockery::mock(ResponseInterface::class);
        $mock->shouldReceive('withAddedHeader')->withAnyArgs()->andReturn($mock);
        $mock->shouldReceive('withStatus')->withAnyArgs()->andReturn($mock);
        $mock->shouldReceive('getBody')->withAnyArgs()->andReturn($stream);

        return $mock;
    }
}
