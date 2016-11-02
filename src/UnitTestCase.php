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

    /**
     * @param object $object
     * @param string $propertyName
     * @param mixed $value
     */
    public function inject($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
