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

use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\ConverterInterface;
use N86io\Rest\Object\Container;
use N86io\Rest\Object\Singleton;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webmozart\Assert\Assert;

/**
 * Class ResponseFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class ResponseFactory implements Singleton
{
    /**
     * @inject
     * @var Container
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
        $this->contentConverter = $this->converterFactory->createFromAccept(
            current($serverRequest->getHeader('accept'))
        );
    }

    /**
     * @param int $status
     * @return ResponseInterface
     */
    public function errorRequest($status)
    {
        Assert::integer($status);
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
     * @return ResponseInterface
     */
    public function internalServerError()
    {
        $errorMessage = [
            '500. Error.',
            'Internal server error.'
        ];
        return $this->createResponse(500, $errorMessage);
    }

    /**
     * @param int $status
     * @param array $content
     * @param int $outputLevel
     * @return ResponseInterface
     */
    public function createResponse($status, array $content, $outputLevel = 0)
    {
        Assert::allInteger([$status, $outputLevel]);
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
