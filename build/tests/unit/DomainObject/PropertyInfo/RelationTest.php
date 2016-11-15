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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo;

use Mockery\MockInterface;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\Relation;
use N86io\Rest\Persistence\ConnectorInterface;
use N86io\Rest\Persistence\Constraint\ConstraintFactory;
use N86io\Rest\Persistence\Constraint\ConstraintInterface;
use N86io\Rest\Persistence\ConstraintUtility;
use N86io\Rest\UnitTestCase;

/**
 * Class RelationTest
 *
 * @author Viktor Firus <v@n86.io>
 */
class RelationTest extends UnitTestCase
{
    public function test()
    {
        $attributes = [
            'type' => get_class(\Mockery::mock(AbstractEntity::class)),
            'constraint' => true,
        ];
        $propertyInfo = $this->createPropertyInfoMock($attributes);
        $this->assertTrue($propertyInfo->isConstraint());
        $propertyInfo->castValue($this->createEntityMock('1,2'));
        $this->assertNull($propertyInfo->castValue($this->createEntityMock('')));

        $this->assertTrue(Relation::verifyAttributes($attributes));
        $attributes['type'] .= '[]';
        $this->assertTrue(Relation::verifyAttributes($attributes));
        $attributes['foreignField'] = 'test';
        $this->assertFalse(Relation::verifyAttributes($attributes));

        $attributes = [
            'type' => get_class(\Mockery::mock(AbstractEntity::class)) . '[]',
            'constraint' => true,
        ];
        $propertyInfo = $this->createPropertyInfoMock($attributes);
        $propertyInfo->castValue($this->createEntityMock('1,2'));
        $this->assertNull($propertyInfo->castValue($this->createEntityMock('')));
    }

    /**
     * @param array $attributes
     * @return MockInterface|PropertyInfoInterface
     */
    protected function createPropertyInfoMock(array $attributes)
    {
        $propertyInfo = \Mockery::mock(Relation::class . '[getEntityInfo]', ['testSomething', $attributes])
            ->shouldReceive('getEntityInfo')->andReturn($this->createEntityInfoMock())->getMock();
        $this->inject($propertyInfo, 'constraintUtility', $this->createConstraintUtilityMock());
        $this->inject($propertyInfo, 'constraintFactory', $this->createConstraintFactoryMock());
        return $propertyInfo;
    }

    /**
     * @param $value
     * @return MockInterface|EntityInterface
     */
    protected function createEntityMock($value)
    {
        return \Mockery::mock(EntityInterface::class)
            ->shouldReceive('getProperty')->with('testSomething')->andReturn($value)->getMock()
            ->shouldReceive('setProperty')->withAnyArgs()->getMock();
    }

    /**
     * @return MockInterface|EntityInfo
     */
    protected function createEntityInfoMock()
    {
        return \Mockery::mock(EntityInfo::class)
            ->shouldReceive('getUidPropertyInfo')->andReturn(\Mockery::mock(PropertyInfoInterface::class))->getMock()
            ->shouldReceive('createConnectorInstance')->andReturn(
                \Mockery::mock(ConnectorInterface::class)
                    ->shouldReceive('setEntityInfo')->withAnyArgs()->getMock()
                    ->shouldReceive('setConstraints')->withAnyArgs()->getMock()
                    ->shouldReceive('read')->andReturn([1 => 'One', 2 => 'Two'])->getMock()
            )->getMock();
    }

    /**
     * @return MockInterface|ConstraintUtility
     */
    protected function createConstraintUtilityMock()
    {
        return \Mockery::mock(ConstraintUtility::class)
            ->shouldReceive('createResourceIdsConstraints')->withAnyArgs()->getMock()
            ->shouldReceive('createEnableFieldsConstraints')->withAnyArgs()->getMock();
    }

    /**
     * @return MockInterface|ConstraintFactory
     */
    protected function createConstraintFactoryMock()
    {
        return \Mockery::mock(ConstraintFactory::class)
            ->shouldReceive('logicalAnd')->withAnyArgs()->andReturn(\Mockery::mock(ConstraintInterface::class))
            ->getMock();
    }
}
