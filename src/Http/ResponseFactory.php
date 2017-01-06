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

namespace N86io\Rest\Http;

use N86io\Di\ContainerInterface;
use N86io\Di\Singleton;
use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\ConverterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class ResponseFactory implements Singleton
{
    /**
     * @inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inject
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
     * @param ServerRequestInterface $serverRequest
     */
    public function setServerRequest(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
        $accept = current($serverRequest->getHeader('accept')) ?: '';
        $this->contentConverter = $this->converterFactory->createFromAccept($accept);
    }

    /**
     * @param int $status
     *
     * @return ResponseInterface
     */
    public function errorCode(int $status): ResponseInterface
    {
        switch ($status) {
            case 400:
                return $this->badRequest();
            case 401:
                return $this->unauthorized();
            case 404:
                return $this->notFound();
            case 405:
                return $this->methodNotAllowed();
        }

        return $this->internalServerError();
    }

    /**
     * @return ResponseInterface
     */
    public function badRequest(): ResponseInterface
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
    public function unauthorized(): ResponseInterface
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
    public function notFound(): ResponseInterface
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
    public function methodNotAllowed(): ResponseInterface
    {
        $errorMessage = [
            '405. Error.',
            'Method not allowed.'
        ];

        return $this->createResponse(405, $errorMessage);
    }

    /**
     * @return ResponseInterface
     */
    public function internalServerError(): ResponseInterface
    {
        $errorMessage = [
            '500. Error.',
            'Internal server error.'
        ];

        return $this->createResponse(500, $errorMessage);
    }

    /**
     * @param int   $status
     * @param array $content
     * @param int   $outputLevel
     *
     * @return ResponseInterface
     */
    public function createResponse(int $status, array $content, int $outputLevel = 0): ResponseInterface
    {
        Assert::greaterThanEq($outputLevel, 0);
        $response = $this->container->get(ResponseInterface::class);
        /** @var ResponseInterface $response */
        $response = $response->withAddedHeader(
            'Content-type',
            $this->contentConverter->getContentType() . '; charset=utf-8'
        );
        $response = $response->withStatus($status);
        $body = $this->contentConverter->render($content, $outputLevel);
        $response->getBody()->write($body);

        return $response;
    }
}
