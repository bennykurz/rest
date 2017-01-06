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

namespace N86io\Rest\Tests\Unit\DomainObject\PropertyInfo;

use Mockery\MockInterface;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoInterface;
use N86io\Rest\DomainObject\EntityInfo\EntityInfoStorage;
use N86io\Rest\DomainObject\EntityInterface;
use N86io\Rest\DomainObject\PropertyInfo\AbstractStatic;
use N86io\Rest\DomainObject\PropertyInfo\PropertyInfoInterface;
use N86io\Rest\DomainObject\PropertyInfo\Relation;
use N86io\Rest\Persistence\ConnectorInterface;
use N86io\Rest\Persistence\Constraint\ConstraintUtility;
use N86io\Rest\Persistence\Constraint\LogicalInterface;
use N86io\Rest\Persistence\RepositoryInterface;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class RelationTest extends UnitTestCase
{
    public function test()
    {
        $type = get_class(\Mockery::mock(AbstractEntity::class));
        $attributes = [
            'constraint' => true,
        ];
        $propertyInfo = $this->createPropertyInfoMock($type, $attributes);
        $this->assertTrue($propertyInfo->isConstraint());
        $propertyInfo->castValue($this->createEntityMock('1,2'));
        $this->assertNull($propertyInfo->castValue($this->createEntityMock('')));

        $this->assertTrue(Relation::checkAttributes($type, $attributes));
        $type .= '[]';
        $this->assertTrue(Relation::checkAttributes($type, $attributes));
        $attributes['foreignField'] = 'test';
        $this->assertFalse(Relation::checkAttributes($type, $attributes));

//        $type .= '[]';
        $attributes = [
            'constraint' => true,
        ];
        $propertyInfo = $this->createPropertyInfoMock($type, $attributes);
        $propertyInfo->castValue($this->createEntityMock('1,2'));
        $this->assertNull($propertyInfo->castValue($this->createEntityMock('')));
    }

    /**
     * @param string $type
     * @param array  $attributes
     *
     * @return MockInterface|PropertyInfoInterface
     */
    protected function createPropertyInfoMock(string $type, array $attributes)
    {
        $propertyInfo = \Mockery::mock(Relation::class . '[getEntityInfo]', ['testSomething', $type, $attributes]);
        $propertyInfo->shouldReceive('getEntityInfo')->andReturn($this->createEntityInfoMock());
        $this->inject($propertyInfo, 'constraintUtility', $this->createConstraintUtilityMock());
        $this->inject($propertyInfo, 'entityInfoStorage', $this->createEntityInfoStorageMock());

        return $propertyInfo;
    }

    /**
     * @return MockInterface|EntityInfoStorage
     */
    protected function createEntityInfoStorageMock()
    {
        $mock = \Mockery::mock(EntityInfoStorage::class);
        $mock->shouldReceive('get')->withAnyArgs()->andReturn(
            \Mockery::mock(EntityInfoInterface::class)
                ->shouldReceive('createRepositoryInstance')->andReturn(
                    \Mockery::mock(RepositoryInterface::class)
                        ->shouldReceive('setConstraints')->getMock()
                        ->shouldReceive('read')->andReturn([])->getMock()
                )->getMock()
                ->shouldReceive('getUidPropertyInfo')->andReturn(
                    \Mockery::mock(AbstractStatic::class)
                )->getMock()
        );

        return $mock;
    }

    /**
     * @param $value
     *
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
        $mock->shouldReceive('createRepositoryInstance')->andReturn(
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
        $mock->shouldReceive('createResourceIdsConstraints')->withAnyArgs()->andReturn(
            \Mockery::mock(LogicalInterface::class)
        );
        $mock->shouldReceive('createEnableFieldsConstraints')->withAnyArgs();

        return $mock;
    }
}
