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

namespace N86io\Rest\DomainObject\PropertyInfo;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class DynamicSelect extends AbstractPropertyInfo implements DynamicSelectInterface
{
    /**
     * @var bool
     */
    protected $ordering;

    /**
     * @var bool
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
     * @param string $name
     * @param string $type
     * @param array  $attributes
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, string $type, array $attributes)
    {
        if (!$this->isSelectOptional && (empty($attributes['select']) || !is_string($attributes['select']) ||
                empty(trim($attributes['select'])))
        ) {
            throw new \InvalidArgumentException('Select should not empty string.');
        }
        parent::__construct($name, $type, $attributes);
    }

    /**
     * @return bool
     */
    public function isOrdering(): bool
    {
        return $this->ordering ?: false;
    }

    /**
     * @return bool
     */
    public function isConstraint(): bool
    {
        return $this->constraint ?: false;
    }

    /**
     * @return string
     */
    public function getSelect(): string
    {
        return $this->select;
    }

    /**
     * @param string $type
     * @param array  $attributes
     *
     * @return bool
     */
    public static function checkAttributes(string $type, array $attributes = []): bool
    {
        return isset($attributes['select']);
    }
}
