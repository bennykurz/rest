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

namespace N86io\Rest\Tests\DomainObject;

use N86io\Rest\DomainObject\AbstractEntity;
use N86io\Rest\Tests\DomainObject;
use N86io\Rest\Tests\DomainObject as Test;

/**
 * Class FakeEntity1
 *
 * Some description
 *
 * @package N86io\Rest\Tests\DomainObject
 */
class FakeEntity1 extends AbstractEntity
{
    /**
     * @var int
     */
    protected $fakeId;

    /**
     * @var string
     */
    protected $string;

    /**
     * @var int
     */
    protected $integer;

    /**
     * @var float
     */
    protected $float;

    /**
     * @var \DateTime
     */
    protected $dateTimeTimestamp;

    /**
     * @var array
     */
    protected $array;

    /**
     * @var FakeEntity1
     */
    protected $demoList;

    /**
     * @var DomainObject\FakeEntity1
     */
    protected $demoList2;

    /**
     * @var Test\FakeEntity1
     */
    protected $demoList3;

    /**
     * @var \N86io\Rest\Tests\DomainObject\FakeEntity1
     */
    protected $demoList4;

    /**
     * @return string
     */
    public function getString()
    {
        return '';
    }

    /**
     * @return boolean
     */
    public function setInteger()
    {
        return true;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeTimestamp()
    {
        return new \DateTime();
    }

    /**
     * @return bool
     */
    public function setDateTimeTimestamp()
    {
        return true;
    }

    /**
     * @return int
     */
    protected function getSomething()
    {
        return 123;
    }
}
