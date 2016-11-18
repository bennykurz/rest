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

/**
 * Class Definition
 *
 * @author Viktor Firus <v@n86.io>
 */
class Definition
{
    const SINGLETON = 0;
    const PROTOTYPE = 1;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var array
     */
    protected $injections = [];

    /**
     * @var bool
     */
    protected $initializeMethod = false;

    /**
     * Definition constructor.
     * @param string $className
     * @param int $type
     */
    public function __construct($className, $type)
    {
        $this->className = $className;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return bool
     */
    public function isSingleton()
    {
        return $this->type === self::SINGLETON;
    }

    /**
     * @return array
     */
    public function getInjections()
    {
        return $this->injections;
    }

    /**
     * @param string $propertyName
     * @param string $className
     */
    public function addInjection($propertyName, $className)
    {
        $this->injections[] = [
            'propertyName' => $propertyName,
            'className' => $className
        ];
    }

    /**
     * @return boolean
     */
    public function hasInitializeMethod()
    {
        return $this->initializeMethod;
    }

    /**
     * @param boolean $initializeMethod
     */
    public function setInitializeMethod($initializeMethod)
    {
        $this->initializeMethod = $initializeMethod;
    }
}
