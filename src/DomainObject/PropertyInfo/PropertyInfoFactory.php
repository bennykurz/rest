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

use N86io\Rest\Object\Container;
use N86io\Rest\Object\Singleton;
use Webmozart\Assert\Assert;

/**
 * Class PropertyInfoFactory
 *
 * @author Viktor Firus <v@n86.io>
 */
class PropertyInfoFactory implements Singleton
{
    /**
     * @inject
     * @var Container
     */
    protected $container;

    /**
     * @var string[]
     */
    protected $propertyInfoClasses = [
        DynamicPhp::class,
        DynamicSelect::class,
        Relation::class,
        RelationOnForeignField::class
    ];

    /**
     * @param string $name
     * @param array $attributes
     * @return PropertyInfoInterface
     */
    public function build($name, array $attributes)
    {
        Assert::string($name);
        foreach ($this->propertyInfoClasses as $propertyInfoClass) {
            if (call_user_func([$propertyInfoClass, 'verifyAttributes'], $attributes)) {
                /** @var PropertyInfoInterface $propertyInfo */
                $propertyInfo = $this->container->get($propertyInfoClass, [$name, $attributes]);
                return $propertyInfo;
            }
        }
        return $this->container->get(Common::class, [$name, $attributes]);
    }

    /**
     * @param string $propertyInfoClass
     */
    public function registerPropertyInfoClass($propertyInfoClass)
    {
        Assert::subclassOf($propertyInfoClass, PropertyInfoInterface::class);
        $this->propertyInfoClasses[] = $propertyInfoClass;
    }

    /**
     * @param string $type
     * @param string $resourcePropertyName
     * @param string $entityClassName
     * @return PropertyInfoInterface
     */
    public function buildEnableField($type, $resourcePropertyName, $entityClassName)
    {
        Assert::oneOf($type, ['deleted', 'disabled', 'startTime', 'endTime']);
        return $this->build(
            $type,
            [
                'type' => 'int',
                'hide' => true,
                'resourcePropertyName' => $resourcePropertyName,
                'entityClassName' => $entityClassName
            ]
        );
    }
}
