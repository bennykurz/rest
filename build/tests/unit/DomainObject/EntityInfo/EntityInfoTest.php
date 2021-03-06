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

namespace N86io\Rest\Tests\Unit\DomainObject\EntityInfo;

use Mockery\MockInterface;
use N86io\Di\Container;
use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\DomainObject\EntityInfo\EntityInfo;
use N86io\Rest\DomainObject\PropertyInfo\AbstractPropertyInfo;
use N86io\Rest\DomainObject\PropertyInfo\Common;
use N86io\Rest\Http\RequestInterface;
use N86io\Rest\Persistence\RepositoryInterface;
use N86io\Rest\UnitTestCase;

/**
 * @author Viktor Firus <v@n86.io>
 */
class EntityInfoTest extends UnitTestCase
{
    /**
     * @var EntityInfo
     */
    protected $entityInfo1;

    /**
     * @var EntityInfo
     */
    protected $entityInfo2;

    /**
     * @var EntityInfo
     */
    protected $entityInfo3;

    /**
     * @var array
     */
    protected $createdPropertyInfo;

    public function test()
    {
        /** @var $entityInfo EntityInfo */
        list($entityClassName, $entityInfo) = $this->createEntityInfoReadOnly();
        $this->assertEquals($entityClassName, $entityInfo->getClassName());
        $this->assertEquals('_table_', $entityInfo->getTable());
        $this->assertTrue($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_READ));
        $this->assertFalse($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_DELETE));
        $this->assertTrue($entityInfo->createRepositoryInstance() instanceof RepositoryInterface);

        $entityInfo = $this->createEntityInfoWriteOnly();
        $this->assertTrue($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_CREATE));
        $this->assertFalse($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_DELETE));

        $entityInfo = $this->createEntityInfoReadWrite();
        $this->assertTrue($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_READ));
        $this->assertTrue($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_CREATE));
        $this->assertTrue($entityInfo->canHandleRequestMode(RequestInterface::REQUEST_MODE_DELETE));

        $this->assertFalse($entityInfo->hasResourceIdPropertyInfo());
        $this->assertFalse($entityInfo->hasUidPropertyInfo());

        $propInfo = $this->createCommonPropertyInfoMock(true, false, 'name', '_name_', 2, 2);
        $entityInfo->addPropertyInfo($propInfo);
        $this->assertTrue($entityInfo->hasResourceIdPropertyInfo());
        $this->assertEquals($propInfo, $entityInfo->getResourceIdPropertyInfo());
        $this->assertEquals($propInfo, $entityInfo->getPropertyInfo('name'));
        $expected = [
            $propInfo->getName() => $propInfo
        ];
        $this->assertEquals($expected, $entityInfo->getPropertyInfoList());
        $this->assertTrue($entityInfo->hasPropertyInfo('name'));
        $this->assertFalse($entityInfo->hasPropertyInfo('otherName'));

        $propInfo = $this->createCommonPropertyInfoMock(false, true, 'name2', '_name_2_', 1, 3);
        $entityInfo->addPropertyInfo($propInfo);
        $this->assertTrue($entityInfo->hasUidPropertyInfo());
        $this->assertEquals($propInfo, $entityInfo->getUidPropertyInfo());

        $propInfo = \Mockery::mock(AbstractPropertyInfo::class);
        $propInfo->shouldReceive('getName')->andReturn('name3');
        $propInfo->shouldReceive('shouldShow')->withAnyArgs()->andReturn(true);
        $propInfo->shouldReceive('getPosition')->andReturn(1);
        $entityInfo->addPropertyInfo($propInfo);

        $this->assertEquals('name3', $entityInfo->getVisiblePropertiesOrdered(0)[0]->getName());
        $this->assertEquals('name', $entityInfo->getVisiblePropertiesOrdered(0)[1]->getName());
        $this->assertEquals('name2', $entityInfo->getVisiblePropertiesOrdered(0)[2]->getName());
    }

    public function testConstructorException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EntityInfo(EntityInfoTest::class, 'table', []);
    }

    public function testSecondResourceIdPropInfoException()
    {
        $entityInfo = $this->createEntityInfoReadWrite();
        $propInfo = $this->createCommonPropertyInfoMock(true, false, 'name', '_name_', 2, 1);
        $entityInfo->addPropertyInfo($propInfo);
        $this->expectException(\InvalidArgumentException::class);
        $propInfo = $this->createCommonPropertyInfoMock(true, false, 'name', '_name_', 2, 1);
        $entityInfo->addPropertyInfo($propInfo);
    }

    public function testSecondUidPropInfoException()
    {
        $entityInfo = $this->createEntityInfoReadWrite();
        $propInfo = $this->createCommonPropertyInfoMock(false, true, 'name', '_name_', 2, 1);
        $entityInfo->addPropertyInfo($propInfo);
        $this->expectException(\InvalidArgumentException::class);
        $propInfo = $this->createCommonPropertyInfoMock(false, true, 'name', '_name_', 2, 1);
        $entityInfo->addPropertyInfo($propInfo);
    }

    /**
     * @param boolean $isResourceId
     * @param boolean $isUid
     * @param string  $name
     * @param string  $resPropName
     * @param int     $outputLevel
     * @param int     $position
     *
     * @return MockInterface|Common
     */
    protected function createCommonPropertyInfoMock($isResourceId, $isUid, $name, $resPropName, $outputLevel, $position)
    {
        $mock = \Mockery::mock(Common::class);
        $mock->shouldReceive('isResourceId')->andReturn($isResourceId);
        $mock->shouldReceive('isUid')->andReturn($isUid);
        $mock->shouldReceive('getName')->andReturn($name);
        $mock->shouldReceive('getResourcePropertyName')->andReturn($resPropName);
        $mock->shouldReceive('getOutputLevel')->andReturn($outputLevel);
        $mock->shouldReceive('shouldShow')->withAnyArgs()->andReturn(true);
        $mock->shouldReceive('getPosition')->andReturn($position);

        return $mock;
    }

    /**
     * @return EntityInfo
     */
    protected function createEntityInfoReadWrite()
    {
        return new EntityInfo(get_class(\Mockery::mock(AbstractEntity::class)), 'table', ['read', 'write']);
    }

    /**
     * @return EntityInfo
     */
    protected function createEntityInfoWriteOnly()
    {
        return new EntityInfo(get_class(\Mockery::mock(AbstractEntity::class)), 'table', ['write']);
    }

    /**
     * @return array
     */
    protected function createEntityInfoReadOnly()
    {
        $entityClassName = get_class(\Mockery::mock(AbstractEntity::class));
        $entityInfo = new EntityInfo($entityClassName, '_table_', ['read'], '_connector_');
        $this->inject($entityInfo, 'container', $this->createContainerMock());

        return [$entityClassName, $entityInfo];
    }

    protected function createContainerMock()
    {
        $mock = \Mockery::mock(Container::class);
        $mock->shouldReceive('get')->withAnyArgs()->andReturn(\Mockery::mock(RepositoryInterface::class));

        return $mock;
    }
}
