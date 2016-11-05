<?php
namespace N86io\Rest;

use DI\Container;
use N86io\Rest\Object\ContainerFactory;
use N86io\Rest\Service\Configuration;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamContent;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Yaml\Yaml;

/**
 * Class UnitTestCase
 * @package N86io\Rest\Test
 */
class UnitTestCase extends \PHPUnit_Framework_TestCase
{
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
