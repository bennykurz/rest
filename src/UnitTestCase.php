<?php
namespace N86io\Rest;

use DI\Container;
use N86io\Rest\Object\ContainerFactory;

/**
 * Class UnitTestCase
 * @package N86io\Rest\Test
 */
class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected static $container;

    public static function setUpBeforeClass()
    {
        static::$container = ContainerFactory::create();
    }
}
