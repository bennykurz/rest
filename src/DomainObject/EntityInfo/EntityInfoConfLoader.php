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

namespace N86io\Rest\DomainObject\EntityInfo;

use N86io\Di\Singleton;
use N86io\Rest\Service\Configuration;

/**
 * @author Viktor Firus <v@n86.io>
 * @since  0.1.0
 */
class EntityInfoConfLoader implements Singleton
{
    /**
     * @inject
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $loadedConf;

    /**
     * @var array
     */
    private $modelDefinition = [
        'table'        => [
            'type' => 'string'
        ],
        'connector'    => [
            'type' => '?string'
        ],
        'mode'         => [
            'type'             => '?uniqueList',
            'allowed-elements' => ['read', 'write']
        ],
        'joins'        => [
            'type'       => '?confList',
            'definition' => [
                'table'     => [
                    'type' => 'string'
                ],
                'condition' => [
                    'type' => 'string'
                ]
            ]
        ],
        'enableFields' => [
            'type'       => '?conf',
            'definition' => [
                'deleted'   => [
                    'type' => '?string'
                ],
                'disabled'  => [
                    'type' => '?string'
                ],
                'startTime' => [
                    'type' => '?string'
                ],
                'endTime'   => [
                    'type' => '?string'
                ]
            ]
        ],
        'properties'   => [
            'type'             => 'confList',
            'definition'       => [
                'uid'                  => [
                    'type'    => 'bool',
                    'default' => false
                ],
                'resourceId'           => [
                    'type'    => 'bool',
                    'default' => false
                ],
                'hide'                 => [
                    'type'    => 'bool',
                    'default' => false
                ],
                'ordering'             => [
                    'type'    => 'bool',
                    'default' => false
                ],
                'constraint'           => [
                    'type'    => 'bool',
                    'default' => false
                ],
                'outputLevel'          => [
                    'type'    => 'int',
                    'default' => 0
                ],
                'position'             => [
                    'type'    => 'int',
                    'default' => 0
                ],
                'resourcePropertyName' => [
                    'type' => '?string'
                ]
            ],
            "definitionOption" => [
                "foreignFieldRelation" => [
                    'foreignField' => [
                        'type' => '?string'
                    ]
                ],
                "dynamicSelect"        => [
                    'sql' => [
                        'type' => '?string'
                    ]
                ]
            ]
        ]
    ];

    /**
     * @param string $className
     * @param array  $parentClassNames
     *
     * @return array
     */
    public function loadSingle(string $className, array $parentClassNames = []): array
    {
        $definition = [
            'type'       => 'conf',
            'definition' => $this->modelDefinition
        ];
        $configuration = new \N86io\ArrayConf\Configuration($definition);
        foreach ($parentClassNames as $parentClassName) {
            if (empty($this->loadedConf[$parentClassName])) {
                continue;
            }
            $configuration->add($this->loadedConf[$parentClassName]);
        }
        $configuration->add($this->loadedConf[$className]);

        return $configuration->get();
    }

    public function __construct()
    {
        $definition = [
            'type'       => 'confList',
            'definition' => $this->modelDefinition
        ];
        $arrayConfiguration = new \N86io\ArrayConf\Configuration($definition);

        $entityInfoConf = $this->configuration->getEntityInfoConfiguration();
        foreach ($entityInfoConf as $item) {
            $content = $item['content'];
            $type = $item['type'];
            if ($type === Configuration::ENTITY_INFO_CONF_JSON_FILE) {
                $content = file_get_contents($content);
            }
            if ($type === Configuration::ENTITY_INFO_CONF_JSON || $type === Configuration::ENTITY_INFO_CONF_JSON_FILE) {
                $content = $this->parseJson($content);
            }
            $arrayConfiguration->add($content);
        }
        $this->loadedConf = $arrayConfiguration->get();
    }

    /**
     * @return array
     */
    public function loadAll(): array
    {
        return $this->loadedConf;
    }

    /**
     * @param string $json
     *
     * @return array
     * @throws \Exception
     */
    private function parseJson(string $json): array
    {
        $array = json_decode($json, true);
        if (!is_array($array)) {
            throw  new \Exception('Invalid JSON.');
        }

        return $array;
    }
}
