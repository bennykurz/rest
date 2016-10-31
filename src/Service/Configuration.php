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

namespace N86io\Rest\Service;

use N86io\Rest\ControllerInterface;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\ObjectContainer;

/**
 * Class Configuration
 * @package N86io\Rest\Service
 */
class Configuration
{
    /**
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * @var array
     */
    protected $apiConfiguration = [];

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param string $apiBaseUrl
     */
    public static function setApiBaseUrl($apiBaseUrl)
    {
        static::getInstance()->apiBaseUrl = static::removeAllSlashesAtEnd($apiBaseUrl);
    }

    /**
     * @param string $string
     * @return string
     */
    protected static function removeAllSlashesAtEnd($string)
    {
        if (substr($string, -1) === '/') {
            $string = substr($string, 0, strlen($string) - 1);
            return static::removeAllSlashesAtEnd($string);
        }
        return $string;
    }

    /**
     * @param string $apiIdentifier
     * @param string $model
     * @param string $version
     */
    public static function registerApiModel($apiIdentifier, $model, $version = '1')
    {
        if (!is_subclass_of($model, EntityInterface::class)) {
            throw new \InvalidArgumentException('The model you want to register should be implements "' .
                EntityInterface::class . '".');
        }
        static::getInstance()->apiConfiguration[$apiIdentifier][$version]['model'] = $model;
    }

    /**
     * @param string $apiIdentifier
     * @param string $controller
     * @param string $version
     */
    public static function registerApiController($apiIdentifier, $controller, $version = '1')
    {
        if (!is_subclass_of($controller, ControllerInterface::class)) {
            throw new \InvalidArgumentException('The controller you want to register should be implements "' .
                ControllerInterface::class . '".');
        }
        static::getInstance()->apiConfiguration[$apiIdentifier][$version]['controller'] = $controller;
    }

    /**
     * @return array
     */
    public function getApiIdentifiers()
    {
        return array_keys($this->apiConfiguration);
    }

    /**
     * @param string $apiIdentifier
     * @return array
     */
    public function getApiConfiguration($apiIdentifier = '')
    {
        if ($apiIdentifier !== '') {
            return $this->apiConfiguration[$apiIdentifier];
        }
        return $this->apiConfiguration;
    }

    /**
     * @return Configuration
     */
    protected static function getInstance()
    {
        return ObjectContainer::get(self::class);
    }
}
