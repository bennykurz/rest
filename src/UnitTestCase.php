<?php
namespace N86io\Rest;

use DI\Container;
use N86io\Rest\Object\ContainerFactory;
use N86io\Rest\Service\Configuration;

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

    /**
     * @var Configuration
     */
    protected static $configuration;

    public static function setUpBeforeClass()
    {
        static::$container = ContainerFactory::create();

        static::$configuration = static::$container->get(Configuration::class);
        static::$configuration->registerEntityInfoConfiguration(
            __DIR__ . '/../tests/Unit/DomainObject/EntityInfoConf.json',
            Configuration::ENTITY_INFO_CONF_FILE + Configuration::ENTITY_INFO_CONF_JSON
        );
        static::$configuration->registerEntityInfoConfiguration(
            __DIR__ . '/../tests/Unit/DomainObject/EntityInfoConf.yml',
            Configuration::ENTITY_INFO_CONF_FILE + Configuration::ENTITY_INFO_CONF_YAML
        );
        static::$configuration->registerEntityInfoConfiguration(
            __DIR__ . '/../tests/Unit/DomainObject/EntityInfoConf.php',
            Configuration::ENTITY_INFO_CONF_FILE + Configuration::ENTITY_INFO_CONF_ARRAY
        );
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
