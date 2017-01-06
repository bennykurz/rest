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

use N86io\Di\ContainerInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use Webmozart\Assert\Assert;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
abstract class AbstractPropertyInfo implements PropertyInfoInterface
{
    /**
     * @inject
     * @var ContainerInterface
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
     * @var bool
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
     * @var array
     */
    protected $rawAttributes = [];

    /**
     * @param string $name
     * @param string $type
     * @param array  $attributes
     */
    public function __construct(string $name, string $type, array $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            if (property_exists($this, $key)) {
                $this->{$key} = $attribute;
            }
        }
        $this->name = $name;
        $this->type = $type;
        $this->rawAttributes = $attributes;
    }

    /**
     * @return EntityInfoInterface
     */
    public function getEntityInfo(): EntityInfoInterface
    {
        /** @var EntityInfo $entityInfo */
        $entityInfo = $this->entityInfoStorage->get($this->entityClassName);

        return $entityInfo;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position ?: 0;
    }

    /**
     * @return string
     */
    public function getGetter(): string
    {
        return $this->getter ?: '';
    }

    /**
     * @return string
     */
    public function getSetter(): string
    {
        return $this->setter ?: '';
    }

    /**
     * @return array
     */
    public function getRawAttributes(): array
    {
        return $this->rawAttributes;
    }

    /**
     * @param int $outputLevel
     *
     * @return bool
     */
    public function shouldShow(int $outputLevel): bool
    {
        Assert::greaterThanEq($outputLevel, 0);
        if (!$this->hide && $outputLevel >= $this->outputLevel) {
            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface $entity
     */
    public function castValue(EntityInterface $entity)
    {
        $value = $entity->getProperty($this->getName());
        switch ($this->type) {
            case 'int':
            case 'integer':
                $value = intval($value, 10);
                break;
            case 'float':
            case 'double':
                $value = floatval($value);
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
     *
     * @return \DateTime
     */
    protected function castDateTime($value): \DateTime
    {
        if (is_numeric($value)) {
            return (new \DateTime())->setTimestamp($value);
        }
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s e', $value . ' UTC');
        if (!$dateTime instanceof \DateTime) {
            $dateTime = (new \DateTime())->setTimestamp(0);
        }
        $dateTime->setTimezone($timezone);

        return $dateTime;
    }
}
