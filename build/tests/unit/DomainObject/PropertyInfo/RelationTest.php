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
        $propertyInfo = \Mockery::mock(Relation::class . '[getEntityInfo]', ['testSomething', $attributes]);
        $propertyInfo->shouldReceive('getEntityInfo')->andReturn($this->createEntityInfoMock());
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
        $mock = \Mockery::mock(EntityInterface::class);
        $mock->shouldReceive('getProperty')->with('testSomething')->andReturn($value);
        $mock->shouldReceive('setProperty')->withAnyArgs();

        return $mock;
    }

    protected function createEntityInfoMock()
    {
        $mock = \Mockery::mock(EntityInfo::class);
        $mock->shouldReceive('getUidPropertyInfo')->andReturn(\Mockery::mock(PropertyInfoInterface::class));
        $mock->shouldReceive('createConnectorInstance')->andReturn(
            \Mockery::mock(ConnectorInterface::class)
                ->shouldReceive('setEntityInfo')->withAnyArgs()->getMock()
                ->shouldReceive('setConstraints')->withAnyArgs()->getMock()
                ->shouldReceive('read')->andReturn([1 => 'One', 2 => 'Two'])->getMock()
        );

        return $mock;
    }

    protected function createConstraintUtilityMock()
    {
        $mock = \Mockery::mock(ConstraintUtility::class);
        $mock->shouldReceive('createResourceIdsConstraints')->withAnyArgs();
        $mock->shouldReceive('createEnableFieldsConstraints')->withAnyArgs();

        return $mock;
    }

    protected function createConstraintFactoryMock()
    {
        $mock = \Mockery::mock(ConstraintFactory::class);
        $mock->shouldReceive('logicalAnd')->withAnyArgs()->andReturn(\Mockery::mock(ConstraintInterface::class));

        return $mock;
    }
}
