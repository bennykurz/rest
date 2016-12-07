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
 * Class DynamicSelect
 *
 * @author Viktor Firus <v@n86.io>
 */
class DynamicSelect extends AbstractPropertyInfo implements DynamicSelectInterface
{
    /**
     * @var boolean
     */
    protected $ordering;

    /**
     * @var boolean
     */
    protected $constraint;

    /**
     * @var string
     */
    protected $select;

    /**
     * @var bool
     */
    protected $isSelectOptional = false;

    /**
     * DynamicSelectPropertyInfo constructor.
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, array $attributes)
    {
        if (!$this->isSelectOptional && (empty($attributes['select']) || !is_string($attributes['select']) ||
                empty(trim($attributes['select'])))
        ) {
            throw new \InvalidArgumentException('Select should not empty string.');
        }
        parent::__construct($name, $attributes);
    }

    /**
     * @return boolean
     */
    public function isOrdering()
    {
        return $this->ordering ?: false;
    }

    /**
     * @return boolean
     */
    public function isConstraint()
    {
        return $this->constraint ?: false;
    }

    /**
     * @return string
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param array $attributes
     * @return boolean
     */
    public static function verifyAttributes(array $attributes)
    {
        return isset($attributes['select']);
    }
}
