<?php


namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\DependencyInjection\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigurationTest
 * @package Actiane\EntityChangeWatchBundle\Tests
 */
class ConfigurationTest extends KernelTestCase
{

    private $validYaml = <<<YAML
classes:
    Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity:
        create:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback', method: 'testCreate'}
        update:
            all:
                - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback', method: 'testUpdate'}
            properties:
                subEntities:
                    - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback', method: 'testUpdateSubEntities'}
        delete:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback', method: 'testDelete'}
YAML;

    private $classDontExistYaml = <<<YAML
classes:
    i\dont\exist:
        create:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback', method: 'testCreate'}
        update:
            all:
                - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback', method: 'testUpdate'}
            properties:
                subEntities:
                    - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback', method: 'testUpdateSubEntities'}
        delete:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback', method: 'testDelete'}
YAML;


    /**
     * @dataProvider dataTestConfiguration
     *
     * @param mixed $inputConfig
     * @param mixed $expectedConfig
     */
    public function testConfiguration($inputConfig, $expectedConfig)
    {
        $configuration = new Configuration();

        $node = $configuration->getConfigTreeBuilder()
                              ->buildTree()
        ;
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    /**
     * @dataProvider dataTestConfigurationInvalid
     *
     * @param mixed $inputConfig
     */
    public function testConfigurationInvalid($inputConfig)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Invalid configuration for path "entity_change_watch.classes": Class not found'
        );
        $configuration = new Configuration();

        $node = $configuration->getConfigTreeBuilder()
                              ->buildTree()
        ;
        $normalizedConfig = $node->normalize($inputConfig);
        $node->finalize($normalizedConfig);
    }

    public function dataTestConfiguration()
    {
        return [
            'test configuration' => [
                Yaml::parse($this->validYaml),
                [
                    'classes' => [
                        'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity' => [
                            'create' => [
                                [
                                    'name' => 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback',
                                    'method' => 'testCreate',
                                ],

                            ],

                            'update' => [
                                'all' => [
                                    [
                                        'name' => 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback',
                                        'method' => 'testUpdate',
                                    ],

                                ],

                                'properties' => [
                                    'subEntities' => [
                                        [
                                            'name' => 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback',
                                            'method' => 'testUpdateSubEntities',
                                        ],

                                    ],

                                ],

                            ],

                            'delete' => [
                                [
                                    'name' => 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback',
                                    'method' => 'testDelete',
                                ],

                            ],

                        ],

                    ],
                ],
            ],
        ];
    }

    public function dataTestConfigurationInvalid()
    {
        return [
            'test configuration' => [
                Yaml::parse($this->classDontExistYaml),
            ],
        ];
    }
}
