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

namespace N86io\Rest\Tests\Reflection;

use N86io\Rest\Reflection\TagUtility;
use N86io\Rest\UnitTestCase;

/**
 * Class TagUtilityTest
 * @package N86io\Rest\Tests\Reflection
 */
class TagUtilityTest extends UnitTestCase
{
    /**
     * @var TagUtility
     */
    protected $tagUtility;

    public function setUp()
    {
        parent::setUp();
        $this->tagUtility = new TagUtility;
    }

    public function testEvaluatePropertyList()
    {
        $propertyList = [
            'val1' => [
                'var' => ['bool'],
                'hide' => ['true']
            ],
            'val2' => [
                'var' => ['string'],
                'outputLevel' => ['123']
            ],
            'val3' => [
                'var' => ['string'],
                'position' => ['0123']
            ]
        ];
        $expected = [
            'val1' => [
                'var' => 'bool',
                'hide' => true
            ],
            'val2' => [
                'var' => 'string',
                'outputLevel' => 123
            ],
            'val3' => [
                'var' => 'string',
                'position' => 123
            ]
        ];
        $actual = $this->tagUtility->evaluatePropertyList($propertyList);
        $this->assertEquals($expected, $actual);
    }

    public function testEvaluateTagList()
    {
        $tagList = [
            'var' => ['float'],
            'outputLevel' => ['2'],
            'position' => ['12'],
            'constraint' => [],
            'hide' => ['false'],
            'something' => 'value'
        ];
        $expected = [
            'var' => 'float',
            'outputLevel' => 2,
            'position' => 12,
            'constraint' => true,
            'hide' => false,
            'something' => 'value'
        ];
        $actual = $this->tagUtility->evaluateTagList($tagList);
        $this->assertEquals($expected, $actual);
    }

    public function testEvaluate()
    {
        $this->assertEquals(true, $this->tagUtility->evaluate('resourceId', ''));
        $this->assertEquals(true, $this->tagUtility->evaluate('resourceId', 'true'));
        $this->assertEquals(true, $this->tagUtility->evaluate('resourceId', '1'));
        $this->assertEquals(false, $this->tagUtility->evaluate('resourceId', '4red'));
        $this->assertEquals(false, $this->tagUtility->evaluate('resourceId', 'false'));
        $this->assertEquals(5, $this->tagUtility->evaluate('outputLevel', '5'));
        $this->assertEquals('int', $this->tagUtility->evaluate('var', 'int '));
        $this->assertEquals('undefinedType', $this->tagUtility->evaluate('var', 'undefinedType'));
        $this->assertEquals(['val1', 'val2'], $this->tagUtility->evaluate('mode', 'val1 val2'));
    }

    public function testMergePropertyList()
    {
        $propertyList1 = [
            'propName' => [
                'outputLevel' => 2,
                'position' => 12,
                'something' => 'value',
                'array' => ['val1', 'val2']
            ],
            'propName2' => [
                'hello1' => 'hello1'
            ]
        ];
        $propertyList2 = [
            'propName' => [
                'var' => 'int',
                'outputLevel' => 10,
                'position' => 21,
                'hide' => false,
                'array' => ['val1']
            ],
            'propName3' => [
                'hello2' => 'hello2'
            ]
        ];
        $expected = [
            'propName' => [
                'outputLevel' => 10,
                'position' => 21,
                'something' => 'value',
                'var' => 'int',
                'hide' => false,
                'array' => ['val1']
            ],
            'propName2' => [
                'hello1' => 'hello1'
            ],
            'propName3' => [
                'hello2' => 'hello2'
            ]
        ];
        $this->assertEquals($expected, $this->tagUtility->mergePropertyList($propertyList1, $propertyList2));
    }

    public function testMergeTagList()
    {
        $tagList1 = [
            'outputLevel' => 2,
            'position' => 12,
            'something' => 'value',
            'array' => ['val1', 'val2']
        ];
        $tagList2 = [
            'var' => 'int',
            'outputLevel' => 10,
            'position' => 21,
            'hide' => false,
            'array' => ['val1']
        ];
        $expected = [
            'outputLevel' => 10,
            'position' => 21,
            'something' => 'value',
            'var' => 'int',
            'hide' => false,
            'array' => ['val1']
        ];
        $this->assertEquals($expected, $this->tagUtility->mergeTagList($tagList1, $tagList2));
    }
}
