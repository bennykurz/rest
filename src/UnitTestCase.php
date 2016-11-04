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
     * @var Container
     */
    protected static $container;

    /**
     * @var Configuration
     */
    protected static $configuration;

    /**
     * @var vfsStreamDirectory
     */
    protected static $streamDirectory;

    public static function setUpBeforeClass()
    {
        static::$container = ContainerFactory::create();
        static::$configuration = static::$container->get(Configuration::class);
        static::$streamDirectory = vfsStream::setup('entityinfoconfs');

        $jsonFile = static::createEntityInfoConfJsonFile(static::$streamDirectory);
        $yamlFile = static::createEntityInfoConfYamlFile(static::$streamDirectory);
        $phpFile = static::createEntityInfoConfPhpFile(static::$streamDirectory);

        static::$configuration->registerEntityInfoConfiguration(
            $jsonFile->url(),
            Configuration::ENTITY_INFO_CONF_FILE + Configuration::ENTITY_INFO_CONF_JSON
        );
        static::$configuration->registerEntityInfoConfiguration(
            $yamlFile->url(),
            Configuration::ENTITY_INFO_CONF_FILE + Configuration::ENTITY_INFO_CONF_YAML
        );
        static::$configuration->registerEntityInfoConfiguration(
            $phpFile->url(),
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

    /**
     * @param vfsStreamDirectory $streamDirectory
     * @return vfsStreamContent
     */
    protected static function createEntityInfoConfJsonFile(vfsStreamDirectory $streamDirectory)
    {
        $content = json_encode([
            'N86io\Rest\Tests\DomainObject\FakeEntity2' => [
                'mode' => ['read'],
                'properties' => [
                    'integer' => ['constraint' => false],
                    'float' => ['hide' => true],
                    'dateTimeTimestamp' => ['outputLevel' => 106],
                    'array' => ['position' => 102],
                    'statusCombined' => [
                        'sqlExpression' => 'CONV(BINARY(CONCAT(%value_a%, %value_b%, %value_c%)),2,10)'
                    ],
                    'statusPhpDetermination' => ['position' => 15, 'outputLevel' => 2]
                ]
            ]
        ]);
        return vfsStream::newFile('EntityInfoConf.json')
            ->withContent($content)
            ->at($streamDirectory);
    }

    /**
     * @param vfsStreamDirectory $streamDirectory
     * @return vfsStreamContent
     */
    protected static function createEntityInfoConfYamlFile(vfsStreamDirectory $streamDirectory)
    {
        $content = Yaml::dump([
            'N86io\Rest\Tests\DomainObject\FakeEntity1' => [
                'table' => 'table_fake',
                'mode' => ['read', 'write'],
                'properties' => [
                    'fakeId' => ['resourcePropertyName' => 'uid', 'resourceId' => true],
                    'string' => ['ordering' => true],
                    'integer' => ['constraint' => true],
                    'float' => ['hide' => false],
                    'dateTimeTimestamp' => ['outputLevel' => 6],
                    'array' => ['position' => 2]
                ]
            ]
        ]);
        return vfsStream::newFile('EntityInfoConf.yaml')
            ->withContent($content)
            ->at($streamDirectory);
    }

    /**
     * @param vfsStreamDirectory $streamDirectory
     * @return vfsStreamContent
     */
    protected static function createEntityInfoConfPhpFile(vfsStreamDirectory $streamDirectory)
    {
        $content = '<?php
return [
    \'N86io\Rest\Tests\DomainObject\FakeEntity4\' => [
        \'table\' => \'table_fake\',
        \'mode\' => [\'read\', \'write\'],
        \'properties\' => [
            \'string\' => [
                \'ordering\' => true
            ]
        ]
    ]
];';
        return vfsStream::newFile('EntityInfoConf.php')
            ->withContent($content)
            ->at($streamDirectory);
    }

    /**
     * @param vfsStreamDirectory $streamDirectory
     * @return vfsStreamContent
     */
    protected static function createEntityInfoConfPhp2File(vfsStreamDirectory $streamDirectory)
    {
        $content = '<?php
return [
    \'N86io\Rest\Tests\DomainObject\FakeEntity1\' => [
        \'table\' => \'table_fake_2\',
        \'properties\' => [
            \'string\' => [\'ordering\' => false, \'hide\' => true]
        ]
    ],
    \'N86io\Rest\Tests\DomainObject\FakeEntity2\' => [
        \'mode\' => [\'write\']
    ],
    \'N86io\Rest\Tests\DomainObject\FakeEntity4\' => [
        \'mode\' => [\'read\']
    ]
];';
        return vfsStream::newFile('EntityInfoConf2.php')
            ->withContent($content)
            ->at($streamDirectory);
    }
}
