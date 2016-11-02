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

namespace N86io\Rest\Tests\Http;

use N86io\Rest\Http\ResponseFactory;
use N86io\Rest\UnitTestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ResponseFactoryTest
 * @package N86io\Rest\Tests\Http
 */
class ResponseFactoryTest extends UnitTestCase
{
    public function test()
    {
        /** @var ServerRequestInterface $serverRequest */
        $serverRequest = \Mockery::mock(ServerRequestInterface::class);
        $serverRequest->shouldReceive('getUri')->andReturn('RequestUrl');

        /** @var ResponseFactory $responseFactory */
        $responseFactory = static::$container->get(ResponseFactory::class);
        $responseFactory->setServerRequest($serverRequest);
        $responseFactory->setAccept('application/json');

        $this->assertEquals(400, $responseFactory->badRequest()->getStatusCode());
        $this->assertEquals(401, $responseFactory->unauthorized()->getStatusCode());
        $this->assertEquals(404, $responseFactory->notFound()->getStatusCode());
        $this->assertEquals(405, $responseFactory->methodNotAllowed()->getStatusCode());
        $this->assertEquals(200, $responseFactory->createResponse(200, [])->getStatusCode());
    }
}
