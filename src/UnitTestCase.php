<?php
namespace N86io\Rest;

/**
 * Class UnitTestCase
 * @package N86io\Rest\Test
 */
class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        ObjectContainer::initialize();
    }
}
