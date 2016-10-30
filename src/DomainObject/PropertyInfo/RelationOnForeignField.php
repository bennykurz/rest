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

namespace N86io\Rest\DomainObject\PropertyInfo;

/**
 * Class RelationOnForeignField
 * @package N86io\Rest\DomainObject\PropertyInfo
 * @Injectable(scope="prototype")
 */
class RelationOnForeignField extends AbstractPropertyInfo implements RelationOnForeignFieldInterface
{
    protected $foreignField;

    /**
     * RelationOnForeignFieldPropertyInfo constructor.
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, array $attributes)
    {
        if (!array_key_exists('foreignField', $attributes) || empty(trim($attributes['foreignField']))) {
            throw new \InvalidArgumentException('ForeignField should not empty string.');
        }
        parent::__construct($name, $attributes);
    }

    /**
     * @return string
     */
    public function getForeignField()
    {
        return $this->foreignField;
    }
}
