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

namespace N86io\Rest;

use DI\Scope;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DependencyFileLoader
 * @package N86io\Rest
 */
class DependencyFileLoader
{
    /**
     * @return array
     */
    public static function load()
    {
        $content = Yaml::parse(file_get_contents(__DIR__ . '/../configuration/Dependency.yml'));
        $result = [];
        foreach ($content as $type => $item) {
            $result[$type] = static::createDependencyObject($item);
        }
        return $result;
    }

    /**
     * @param array $item
     * @return object
     */
    protected static function createDependencyObject(array $item)
    {
        $object = \DI\object($item['object']);
        if (array_key_exists('scope', $item)) {
            switch ($item['scope']) {
                case 'prototype':
                    $object->scope(Scope::PROTOTYPE);
                    break;
                case 'singleton':
                    $object->scope(Scope::SINGLETON);
                    break;
            }
        }
        return $object;
    }
}
