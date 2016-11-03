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

namespace N86io\Rest\DomainObject\EntityInfo;

use N86io\Rest\Service\Configuration;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EntityInfoConfLoader
 * @package N86io\Rest\DomainObject\EntityInfo
 */
class EntityInfoConfLoader
{
    /**
     * @Inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @return array
     */
    public function load()
    {
        $entityInfoConf = $this->configuration->getEntityInfoConfiguration();
        $result = [];
        foreach ($entityInfoConf as $item) {
            $content = $item['content'];
            $type = $item['type'];
            if (($type & Configuration::ENTITY_INFO_CONF_FILE) !== 0) {
                $content = $this->loadFromFile($content, $type);
            }
            if (($type & Configuration::ENTITY_INFO_CONF_JSON) !== 0) {
                $content = $this->parseJson($content);
            }
            if (($type & Configuration::ENTITY_INFO_CONF_YAML) !== 0) {
                $content = $this->parseYaml($content);
            }
            $this->mergeComplete($result, $content);
        }
        return $result;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function mergeComplete(array &$array1, array $array2)
    {
        foreach ($array2 as $modelName2 => $modelConf2) {
            if (!array_key_exists($modelName2, $array1)) {
                $array1[$modelName2] = [];
            }
            $modelConf1 = &$array1[$modelName2];
            $this->mergeSingle($modelConf1, $modelConf2, 'table');
            $this->mergeSingle($modelConf1, $modelConf2, 'mode');

            if (!array_key_exists('properties', $modelConf2)) {
                continue;
            }

            if (!array_key_exists('properties', $modelConf1)) {
                $modelConf1['properties'] = [];
            }
            $this->mergeProperties($modelConf1['properties'], $modelConf2['properties']);
        }
        return $array1;
    }

    /**
     * @param array $properties1
     * @param array $properties2
     */
    protected function mergeProperties(array &$properties1, array $properties2)
    {
        foreach ($properties2 as $propertyName => $property2) {
            if (!array_key_exists($propertyName, $properties1)) {
                $properties1[$propertyName] = [];
            }
            $property1 = &$properties1[$propertyName];
            foreach (array_keys($property2) as $attributeName) {
                $this->mergeSingle($property1, $property2, $attributeName);
            }
        }
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param string $key
     */
    protected function mergeSingle(array &$array1, array $array2, $key)
    {
        if (array_key_exists($key, $array2)) {
            $array1[$key] = $array2[$key];
        }
    }

    /**
     * @param string $yaml
     * @return mixed
     * @throws \Exception
     */
    protected function parseYaml($yaml)
    {
        $array = Yaml::parse($yaml);
        if (!is_array($array)) {
            throw  new \Exception('Invalid YAML or configuration given.');
        }
        return $array;
    }

    /**
     * @param string $json
     * @return array
     * @throws \Exception
     */
    protected function parseJson($json)
    {
        $array = json_decode($json, true);
        if (!is_array($array)) {
            throw  new \Exception('Invalid JSON or configuration given.');
        }
        return $array;
    }

    /**
     * @param $path
     * @param $type
     * @return array|string
     * @throws \Exception
     */
    protected function loadFromFile($path, $type)
    {
        if (($type & Configuration::ENTITY_INFO_CONF_ARRAY) !== 0) {
            $array = require $path;
            if (!is_array($array)) {
                throw new \Exception('The file "' . $path . '" gives no array return.');
            }
            return $array;
        }
        return file_get_contents($path);
    }
}
