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

namespace N86io\Rest\Object;

use Doctrine\Common\Cache\Cache;
use N86io\Reflection\ReflectionClass;

/**
 * Class DefinitionResolver
 *
 * @author Viktor Firus <v@n86.io>
 */
class DefinitionResolver
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * DefinitionResolver constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $className
     * @return Definition
     */
    public function get($className)
    {
        if (!$this->cache->contains($className)) {
            $reflectionClass = new ReflectionClass($className);

            $interfaces = $reflectionClass->getInterfaceNames();
            $isSingleton = array_search(SingletonInterface::class, $interfaces) !== false;
            $type = $isSingleton ? Definition::SINGLETON : Definition::PROTOTYPE;
            $definition = new Definition($className, $type);

            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
                $docComment = $property->getParsedDocComment();
                if ($docComment->hasTag('inject')) {
                    $injectClassName = current($docComment->getTagsByName('var'));
                    $injectClassName = $injectClassName[0] === '\\' ? substr($injectClassName, 1) : $injectClassName;
                    $definition->addInjection(
                        $property->getName(),
                        $injectClassName
                    );
                }
            }
            $this->cache->save($className, $definition);
        }
        return $this->cache->fetch($className);
    }
}
