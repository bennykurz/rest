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

use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\Object\Container;

/**
 * Class AbstractPropertyInfo
 *
 * @author Viktor Firus <v@n86.io>
 */
abstract class AbstractPropertyInfo implements PropertyInfoInterface
{
    /**
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @inject
     * @var EntityInfoStorage
     */
    protected $entityInfoStorage;

    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $hide;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
    protected $outputLevel;

    /**
     * @var string
     */
    protected $getter;

    /**
     * @var string
     */
    protected $setter;

    /**
     * AbstractPropertyInfo constructor.
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, array $attributes)
    {
        if (!array_key_exists('type', $attributes) || empty(trim($attributes['type']))) {
            throw new \InvalidArgumentException('Missed type for PropertyInfo (' . $name . ').');
        }
        foreach ($attributes as $key => $attribute) {
            if (property_exists($this, $key)) {
                $this->{$key} = $attribute;
            }
        }
        $this->name = $name;
    }

    /**
     * @return EntityInfo
     */
    public function getEntityInfo()
    {
        /** @var EntityInfo $entityInfo */
        $entityInfo = $this->entityInfoStorage->get($this->entityClassName);
        return $entityInfo;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position ?: 0;
    }

    /**
     * @return string
     */
    public function getGetter()
    {
        return $this->getter ?: '';
    }

    /**
     * @return string
     */
    public function getSetter()
    {
        return $this->setter ?: '';
    }

    /**
     * @param int $outputLevel
     * @return boolean
     */
    public function shouldShow($outputLevel)
    {
        if (!$this->hide && $outputLevel >= $this->outputLevel) {
            return true;
        }
        return false;
    }

    /**
     * @param AbstractEntity $entity
     */
    public function castValue(AbstractEntity $entity)
    {
        $value = $entity->getProperty($this->getName());
        switch ($this->type) {
            case 'int':
            case 'integer':
                $value = intval($value, 10);
                break;
            case 'float':
                $value = floatval($value);
                break;
            case 'double':
                $value = doubleval($value);
                break;
            case 'bool':
            case 'boolean':
                $value = boolval($value);
                break;
            case 'DateTime':
            case '\DateTime':
                $value = $this->castDateTime($value);
                break;
        }
        $entity->setProperty($this->getName(), $value);
    }

    /**
     * @param $value
     * @return \DateTime
     */
    protected function castDateTime($value)
    {
        if (is_numeric($value)) {
            return (new \DateTime())->setTimestamp($value);
        }
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s e', $value . ' UTC')->setTimezone($timezone);
        return $dateTime;
    }
}
