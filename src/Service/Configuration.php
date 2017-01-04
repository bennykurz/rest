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

namespace N86io\Rest\Service;

use N86io\Di\Singleton;
use N86io\Rest\ControllerInterface;
use N86io\Rest\DomainObject\EntityInterface;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class Configuration implements Singleton
{
    const ENTITY_INFO_CONF_ARRAY = 1;
    const ENTITY_INFO_CONF_JSON = 2;
    const ENTITY_INFO_CONF_JSON_FILE = 3;

    /**
     * @var string
     */
    protected $apiBaseUrl = '';

    /**
     * @var array
     */
    protected $apiConfiguration = [];

    /**
     * @var array
     */
    protected $apiAliases = [];

    /**
     * @var array
     */
    protected $apiControllerSettings = [];

    /**
     * @var array
     */
    protected $entityInfoConf = [];

    /**
     * Takes all configuration from given instance.
     *
     * @param Configuration $configuration
     *
     * @internal
     */
    public function overrideConfiguration(Configuration $configuration)
    {
        $this->apiBaseUrl = $configuration->apiBaseUrl;
        $this->apiConfiguration = $configuration->apiConfiguration;
        $this->apiAliases = $configuration->apiAliases;
        $this->apiControllerSettings = $configuration->apiControllerSettings;
        $this->entityInfoConf = $configuration->entityInfoConf;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param string $apiBaseUrl
     */
    public function setApiBaseUrl(string $apiBaseUrl)
    {
        $this->apiBaseUrl = $this->removeAllSlashesAtEnd($apiBaseUrl);
    }

    /**
     * @return array
     */
    public function getApiIdentifiers(): array
    {
        return array_merge(
            array_keys($this->apiConfiguration),
            array_keys($this->apiAliases)
        );
    }

    /**
     * @param string $apiIdentifier
     *
     * @return array
     */
    public function getApiConfiguration(string $apiIdentifier): array
    {
        if ($this->isApiAlias($apiIdentifier)) {
            $apiIdentifier = $this->apiAliases[$apiIdentifier];
        }

        return $this->apiConfiguration[$apiIdentifier];
    }

    /**
     * @param string $apiIdentifier
     *
     * @return array
     */
    public function getApiControllerSettings(string $apiIdentifier): array
    {
        if (isset($this->apiControllerSettings[$apiIdentifier])) {
            return $this->apiControllerSettings[$apiIdentifier];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getEntityInfoConfiguration(): array
    {
        return $this->entityInfoConf;
    }

    /**
     * @param string $apiIdentifier
     * @param string $model
     * @param string $version
     *
     * @throws \InvalidArgumentException
     */
    public function registerApiModel(string $apiIdentifier, string $model, string $version = '1')
    {
        $this->makeSureNotAlias($apiIdentifier);
        if (!is_subclass_of($model, EntityInterface::class)) {
            throw new \InvalidArgumentException('The model you want to register should be implements "' .
                EntityInterface::class . '".');
        }
        $this->apiConfiguration[$apiIdentifier][$version]['model'] = $model;
    }

    /**
     * @param string $apiIdentifier
     * @param string $controller
     * @param string $version
     *
     * @throws \InvalidArgumentException
     */
    public function registerApiController(string $apiIdentifier, string $controller, string $version = '1')
    {
        $this->makeSureNotAlias($apiIdentifier);
        if (!is_subclass_of($controller, ControllerInterface::class)) {
            throw new \InvalidArgumentException('The controller you want to register should be implements "' .
                ControllerInterface::class . '".');
        }
        $this->apiConfiguration[$apiIdentifier][$version]['controller'] = $controller;
    }

    /**
     * @param string $apiAlias
     * @param string $apiIdentifier
     *
     * @throws \InvalidArgumentException
     */
    public function registerAlias(string $apiAlias, string $apiIdentifier)
    {
        if ($this->isApiAlias($apiIdentifier)) {
            throw new \InvalidArgumentException('Can\'t register api-alias "' . $apiAlias . '" on another alias "' .
                $apiIdentifier . '".');
        }
        if ($this->isRegularApiIdentifier($apiAlias)) {
            throw new \InvalidArgumentException('"' . $apiAlias . '" is already registered as api-identifier and ' .
                'can\'t use as api-alias.');
        }
        if (!$this->isRegularApiIdentifier($apiIdentifier)) {
            throw new \InvalidArgumentException('Can\'t find api-identifier "' . $apiIdentifier . '".');
        }
        $this->apiAliases[$apiAlias] = $apiIdentifier;
    }

    /**
     * @param string $apiIdentifier
     * @param array  $settings
     */
    public function registerApiControllerSettings(string $apiIdentifier, array $settings)
    {
        $this->apiControllerSettings[$apiIdentifier] = $settings;
    }

    /**
     * @param $content
     */
    public function registerEntityInfoConfiguration($content)
    {
        $this->entityInfoConf[] = [
            'type'    => $this->getEntityInfoType($content),
            'content' => $content
        ];
    }

    /**
     * @param mixed $content
     *
     * @return int
     */
    protected function getEntityInfoType($content): int
    {
        if (is_array($content)) {
            return self::ENTITY_INFO_CONF_ARRAY;
        }
        if (is_string($content) && is_readable($content)) {
            return self::ENTITY_INFO_CONF_JSON_FILE;
        }

        return self::ENTITY_INFO_CONF_JSON;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function removeAllSlashesAtEnd(string $string): string
    {
        if (substr($string, -1) === '/') {
            $string = substr($string, 0, strlen($string) - 1);

            return $this->removeAllSlashesAtEnd($string);
        }

        return $string;
    }

    /**
     * @param string $apiIdentifier
     *
     * @throws \InvalidArgumentException
     */
    protected function makeSureNotAlias(string $apiIdentifier)
    {
        if ($this->isApiAlias($apiIdentifier)) {
            throw new \InvalidArgumentException('"' . $apiIdentifier . '" is already registered as api-alias and ' .
                'can\'t use as api-identifier.');
        }
    }

    /**
     * @param string $apiIdentifier
     *
     * @return bool
     */
    protected function isApiAlias(string $apiIdentifier): bool
    {
        return isset($this->apiAliases[$apiIdentifier]);
    }

    /**
     * @param string $apiIdentifier
     *
     * @return bool
     */
    protected function isRegularApiIdentifier(string $apiIdentifier): bool
    {
        return isset($this->apiConfiguration[$apiIdentifier]);
    }
}
