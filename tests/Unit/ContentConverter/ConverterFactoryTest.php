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

namespace N86io\Rest\Tests\ContentConverter;

use N86io\Rest\ContentConverter\ConverterFactory;
use N86io\Rest\ContentConverter\JsonConverter;
use N86io\Rest\ObjectContainer;

/**
 * Class ConverterFactoryTest
 * @package N86io\Rest\Tests\ContentConverter
 */
class ConverterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        /** @var ConverterFactory $converterFactory */
        $converterFactory = ObjectContainer::get(ConverterFactory::class);
        $this->assertTrue($converterFactory->createFromAccept('application/json') instanceof JsonConverter);
        $this->assertTrue($converterFactory->createFromAccept('') instanceof JsonConverter);
    }
}
