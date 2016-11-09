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
 * Class Common
 *
 * @author Viktor Firus <v@n86.io>
 */
class Common extends AbstractStatic implements
    ResourceIdInterface,
    SortableInterface,
    RestrictableInterface,
    UidInterface
{
    /**
     * @var boolean
     */
    protected $resourceId;

    /**
     * @var boolean
     */
    protected $ordering;

    /**
     * @var boolean
     */
    protected $constraint;

    /**
     * @var boolean
     */
    protected $uid;

    /**
     * @return boolean
     */
    public function isResourceId()
    {
        return $this->resourceId === true;
    }

    /**
     * @return boolean
     */
    public function isConstraint()
    {
        return $this->constraint === true;
    }

    /**
     * @return boolean
     */
    public function isOrdering()
    {
        return $this->ordering === true;
    }

    /**
     * @return boolean
     */
    public function isUid()
    {
        return $this->uid === true;
    }
}
