<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\DependencyInjection\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;

/**
 * Class ConfigurationTest
 *
 * @package Actiane\EntityChangeWatchBundle\Tests
 */
class ConfigurationTest extends KernelTestCase
{
    private string $validYaml = <<<YAML
classes:
    Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity:
        create:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback', method: 'testCreate', flush: false}
        update:
            all:
                - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback', method: 'testUpdate', flush: false}
            properties:
                subEntities:
                    - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback', method: 'testUpdateSubEntities', flush: false}
        delete:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback', method: 'testDelete', flush: false}
YAML;
    private string $classDontExistYaml = <<<YAML
classes:
    i\dont\exist:
        create:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback', method: 'testCreate', flush: false}
        update:
            all:
                - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback', method: 'testUpdate', flush: false}
            properties:
                subEntities:
                    - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback', method: 'testUpdateSubEntities', flush: false}
        delete:
            - {name: 'Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback', method: 'testDelete', flush: false}
YAML;

    /**
     * @dataProvider dataTestConfiguration
     *
     * @small
     *
     * @param mixed $inputConfig
     * @param mixed $expectedConfig
     */
    public function testConfiguration($inputConfig, $expectedConfig): void
    {
        $configuration = new Configuration();

        $node = $configuration->getConfigTreeBuilder()
            ->buildTree();
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    /**
     * @dataProvider dataTestConfigurationInvalid
     *
     * @small
     *
     * @param mixed $inputConfig
     */
    public function testConfigurationInvalid($inputConfig): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(
            'Invalid configuration for path "entity_change_watch.classes": Class not found'
        );
        $configuration = new Configuration();

        $node = $configuration->getConfigTreeBuilder()
            ->buildTree();
        $normalizedConfig = $node->normalize($inputConfig);
        $node->finalize($normalizedConfig);
    }

    /**
     * @return array[]
     */
    public function dataTestConfiguration(): array
    {
        return [
            'test configuration' => [
                Yaml::parse($this->validYaml),
                [
                    'classes' => [
                        Entity::class => [
                            'create' => [
                                [
                                    'name' => EntityCreateCallback::class,
                                    'method' => 'testCreate',
                                    'flush' => false,
                                ],

                            ],

                            'update' => [
                                'all' => [
                                    [
                                        'name' => EntityUpdateCallback::class,
                                        'method' => 'testUpdate',
                                        'flush' => false,
                                    ],

                                ],

                                'properties' => [
                                    'subEntities' => [
                                        [
                                            'name' => EntityUpdateSubEntitiesCallback::class,
                                            'method' => 'testUpdateSubEntities',
                                            'flush' => false,
                                        ],

                                    ],

                                ],

                            ],

                            'delete' => [
                                [
                                    'name' => EntityDeleteCallback::class,
                                    'method' => 'testDelete',
                                    'flush' => false,
                                ],

                            ],

                        ],

                    ],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function dataTestConfigurationInvalid(): array
    {
        return [
            'test configuration' => [
                Yaml::parse($this->classDontExistYaml),
            ],
        ];
    }
}
