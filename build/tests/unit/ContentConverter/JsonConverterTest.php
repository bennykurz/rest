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

namespace N86io\Rest\Tests\Unit\ContentConverter;

use N86io\Rest\ContentConverter\JsonConverter;

/**
 * @author Viktor Firus <v@n86.io>
 */
class JsonConverterTest extends AbstractConverterTest
{
    public function test()
    {
        $converter = new JsonConverter;
        $this->inject($converter, 'entityInfoStorage', $this->createEntityInfoStorageMock());
        $this->inject($converter, 'authorization', $this->createAuthorizationMock());

        $expected = '{"key1":"value1","key2":{"key2_1":"value2"},"key3":{"nameOne":"_name_one_",' .
            '"nameTwo":"_name_two_"},"key4":"2016-11-15_10:42:26","key5":"valueOf5"}';
        $this->assertEquals($expected, $converter->render($this->createList(), 0));
        $this->assertEquals('application/json', $converter->getContentType());

        $jsonValue = '{"irgendwas":"wert"}';
        $arrayValue = [
            'irgendwas' => 'wert'
        ];
        $this->assertEquals($arrayValue, $converter->parse($jsonValue));
    }
}
