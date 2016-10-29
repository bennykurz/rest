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

namespace N86io\Rest\Reflection;

/**
 * Class TagUtility
 * @package N86io\Rest\Reflection
 */
class TagUtility
{
    /**
     * @var array
     */
    protected $types = [
        'resourceId' => 'bool',
        'ordering' => 'bool',
        'constraint' => 'bool',
        'hide' => 'bool',
        'outputLevel' => 'int',
        'position' => 'int',
        'mode' => 'spaceSeparatedList'
    ];

    /**
     * @param array $properties
     * @return array
     */
    public function evaluatePropertyList(array $properties)
    {
        foreach ($properties as &$propertyTags) {
            $propertyTags = $this->evaluateTagList($propertyTags);
        }
        return $properties;
    }

    /**
     * @param array $list
     * @return array
     */
    public function evaluateTagList(array $list)
    {
        foreach ($list as $tagName => &$tagValue) {
            $tagValue = $this->evaluate($tagName, $tagValue);
        }
        return $list;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function evaluate($name, $value)
    {
        $value = is_array($value) ? trim(current($value)) : trim($value);
        $switchVal = array_key_exists($name, $this->types) ? $this->types[$name] : '';
        switch ($switchVal) {
            case 'bool':
                return trim($value) === '1' || trim(strtolower($value)) === 'true' || trim($value) == '';
            case 'int':
                return intval($value, 10);
            case 'spaceSeparatedList':
                return explode(' ', $value);
        }
        return $value;
    }

    /**
     * @param array $properties1
     * @param array $properties2
     * @return array
     */
    public function mergePropertyList(array $properties1, array $properties2)
    {
        foreach ($properties2 as $propertyName => $propertyTags2) {
            $propertyTags1 = &$properties1[$propertyName];
            $propertyTags1 = $propertyTags1 ?: [];
            $propertyTags1 = $this->mergeTagList($propertyTags1, $propertyTags2);
        }
        return $properties1;
    }

    /**
     * @param array $list1
     * @param array $list2
     * @return array
     */
    public function mergeTagList(array $list1, array $list2)
    {
        foreach ($list2 as $key => $value) {
            $list1[$key] = $value;
        }
        return $list1;
    }
}
