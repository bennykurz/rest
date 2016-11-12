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

use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\ConstraintUtility;

/**
 * Class Relation
 *
 * @author Viktor Firus <v@n86.io>
 */
class Relation extends AbstractStatic implements RestrictableInterface
{
    /**
     * @inject
     * @var ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @inject
     * @var ConstraintUtility
     */
    protected $constraintUtility;

    /**
     * @var boolean
     */
    protected $constraint;

    /**
     * @return boolean
     */
    public function isConstraint()
    {
        return $this->constraint ?: false;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function castValue($value)
    {
        $isList = substr($this->type, -2) === '[]';
        if ($isList && empty(trim($value))) {
            return [];
        } elseif (empty(trim($value))) {
            return '';
        }
        $entityInfo = $this->getEntityInfo();

        $resourceIds = explode(',', $value);
        $constraints = [
            $this->constraintUtility->createResourceIdsConstraints(
                $entityInfo->getUidPropertyInfo(),
                $resourceIds
            )
        ];
        $constraints[] = $this->constraintUtility->createEnableFieldsConstraints($entityInfo);
        $constraints = $this->constraintFactory->logicalAnd($constraints);

        $connector = $entityInfo->createConnectorInstance();
        $connector->setEntityInfo($entityInfo);
        $connector->setConstraints($constraints);

        $result = $connector->read();

        if ($isList) {
            return $result;
        }

        return current($result);
    }
}
