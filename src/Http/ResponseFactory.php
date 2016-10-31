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

namespace N86io\Rest\Http;

use DI\Container;
use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\ConverterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ResponseFactory
 * @package N86io\Rest\Http
 */
class ResponseFactory
{
    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * @Inject
     * @var ConverterFactory
     */
    protected $converterFactory;

    /**
     * @var ConverterInterface
     */
    protected $contentConverter;

    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

    /**
     * @param string $accept
     */
    public function setAccept($accept)
    {
        $this->contentConverter = $this->converterFactory->createFromAccept($accept);
    }

    /**
     * @param ServerRequestInterface $serverRequest
     */
    public function setServerRequest(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function badRequest()
    {
        $errorMessage = [
            '400. Error.',
            'Bad request.'
        ];
        return $this->createResponse(400, $errorMessage);
    }

    /**
     * @return ResponseInterface
     */
    public function unauthorized()
    {
        $errorMessage = [
            '401. Error.',
            'Unauthorized.'
        ];
        return $this->createResponse(401, $errorMessage);
    }

    /**
     * @return ResponseInterface
     */
    public function notFound()
    {
        $errorMessage = [
            '404. Error.',
            'The requested URL \'' . $this->serverRequest->getUri() . '\' has no match.'
        ];
        return $this->createResponse(404, $errorMessage);
    }

    /**
     * @return ResponseInterface
     */
    public function methodNotAllowed()
    {
        $errorMessage = [
            '405. Error.',
            'Method not allowed.'
        ];
        return $this->createResponse(405, $errorMessage);
    }

    /**
     * @param int $status
     * @param array $content
     * @return ResponseInterface
     */
    public function createResponse($status, array $content)
    {
        $response = $this->container->make(ResponseInterface::class);
        /** @var ResponseInterface $response */
        $response = $response->withAddedHeader(
            'Content-type',
            $this->contentConverter->getContentType() . '; charset=utf-8'
        );
        $response = $response->withStatus($status);
        $body = $this->contentConverter->render($content);
        $response->getBody()->write($body);
        return $response;
    }
}