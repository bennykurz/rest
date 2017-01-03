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

use N86io\Di\Singleton;
use N86io\Rest\Service\Configuration;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

/**
 * Class EntityInfoConfLoader
 *
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoConfLoader implements Singleton
{
    /**
     * @inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $loadedConf;

    /**
     * @param string $className
     * @param array $parentClassNames
     * @return array
     */
    public function loadSingle($className, array $parentClassNames = [])
    {
        Assert::string($className);
        $this->loadAll();
        $result = [];
        foreach ($parentClassNames as $parentClassName) {
            $this->addEmptyEntityInfoConf($parentClassName);
            $this->mergeModelConf($result, $this->loadedConf[$parentClassName]);
        }
        $this->addEmptyEntityInfoConf($className);
        $this->mergeModelConf($result, $this->loadedConf[$className]);
        return $result;
    }

    /**
     * @param string $className
     */
    protected function addEmptyEntityInfoConf($className)
    {
        if (empty($this->loadedConf[$className])) {
            $this->loadedConf[$className] = [
                'properties' => []
            ];
        }
    }

    /**
     * @return array
     */
    public function loadAll()
    {
        if ($this->loadedConf) {
            return $this->loadedConf;
        }
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
        $this->loadedConf = $result;
        return $result;
    }

    /**
     * @param array $array1
     * @param array $array2
     */
    protected function mergeComplete(array &$array1, array $array2)
    {
        foreach ($array2 as $modelName2 => $modelConf2) {
            if (empty($array1[$modelName2])) {
                $array1[$modelName2] = [];
            }
            $modelConf1 = &$array1[$modelName2];
            $this->mergeModelConf($modelConf1, $modelConf2);
        }
    }

    /**
     * @param array $modelConf1
     * @param array $modelConf2
     */
    protected function mergeModelConf(array &$modelConf1, array $modelConf2)
    {
        $this->mergeSingle($modelConf1, $modelConf2, ['table', 'mode', 'connector']);
        if (isset($modelConf2['enableFields'])) {
            if (empty($modelConf1['enableFields'])) {
                $modelConf1['enableFields'] = [];
            }
            $this->mergeSingle(
                $modelConf1['enableFields'],
                $modelConf2['enableFields'],
                ['deleted', 'disabled', 'startTime', 'endTime']
            );
        }
        if (isset($modelConf2['joins'])) {
            if (empty($modelConf1['joins'])) {
                $modelConf1['joins'] = [];
            }
            $this->mergeJoins($modelConf1['joins'], $modelConf2['joins']);
        }
        if (empty($modelConf1['properties'])) {
            $modelConf1['properties'] = [];
        }
        $this->mergeProperties($modelConf1['properties'], $modelConf2['properties']);
    }

    /**
     * @param array $joins1
     * @param array $joins2
     */
    protected function mergeJoins(array &$joins1, array $joins2)
    {
        foreach ($joins2 as $aliasName => $join2) {
            if (empty($joins1[$aliasName])) {
                $joins1[$aliasName] = [];
            }
            $join1 = &$joins1[$aliasName];
            $this->mergeSingle($join1, $join2, array_keys($join2));
        }
    }

    /**
     * @param array $properties1
     * @param array $properties2
     */
    protected function mergeProperties(array &$properties1, array $properties2)
    {
        foreach ($properties2 as $propertyName => $attributes2) {
            if (empty($properties1[$propertyName])) {
                $properties1[$propertyName] = [];
            }
            $attributes1 = &$properties1[$propertyName];
            $this->mergeSingle($attributes1, $attributes2, array_keys($attributes2));
        }
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param array $keys
     */
    protected function mergeSingle(array &$array1, array $array2, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($array2[$key])) {
                $array1[$key] = $array2[$key];
            }
        }
    }

    /**
     * @param string $yaml
     * @return array
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
            return require $path;
        }
        return file_get_contents($path);
    }
}
