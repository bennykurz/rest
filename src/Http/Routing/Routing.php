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

namespace N86io\Rest\Http\Routing;

use N86io\Rest\Service\Configuration;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Routing
 * @package N86io\Rest\Http\Routing
 * @Injectable(scope="prototype")
 */
class Routing implements RoutingInterface
{
    /**
     * @Inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var RoutingParameter[]
     */
    protected $parameters = [];

    /**
     * @param RoutingParameterInterface $routingParameter
     */
    public function addParameter(RoutingParameterInterface $routingParameter)
    {
        $this->parameters[$routingParameter->getName()] = $routingParameter;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return array
     */
    public function getRoute(ServerRequestInterface $serverRequest)
    {
        $patternPart = [];
        foreach ($this->parameters as $parameter) {
            if ($parameter->isOptional()) {
                $patternPart[] = '(\/' . $parameter->getExpression() . ')?';
                continue;
            }
            $patternPart[] = '(\/' . $parameter->getExpression() . '){1}';
        }
        $searchPattern = '/^' . implode('', $patternPart) . '$/';
        $apiPath = $this->getApiPath($serverRequest);

        preg_match($searchPattern, $apiPath, $matches);

        if (empty($matches)) {
            return [];
        }
        $matches = array_slice($matches, 1);

        $position = -1;
        $route = [];
        foreach ($this->parameters as $parameter) {
            $position += $parameter->getTakeResult();
            if (empty($matches[$position])) {
                continue;
            }
            $value = $matches[$position][0] !== '/' ? $matches[$position] : substr($matches[$position], 1);
            $route[$parameter->getName()] = $value;
        }

        return $route;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getApiPath(ServerRequestInterface $request)
    {
        $apiBaseUrl = $this->configuration->getApiBaseUrl();
        $url = $this->getUrl($request);
        return substr($url, strlen($apiBaseUrl));
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getUrl(ServerRequestInterface $request)
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
}
