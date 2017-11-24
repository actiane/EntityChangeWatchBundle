<?php

namespace Actiane\EntityChangeWatchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('entity_change_watch');


        $rootNode->children()
                    ->arrayNode('classes')->validate()->ifTrue(function($classes){

                        foreach($classes as $key=>$value)
                        {
                            if (!class_exists($key))
                                return $key;
                        }
                        return false;

            })->thenInvalid('Class inexistante')->end()
                        ->prototype('array')
                            ->children()
                                ->arrayNode('update')
                                    ->children()
                                        ->arrayNode('all')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('method')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('properties')
                                            ->prototype('array')
                                                ->prototype('array')
                                                    ->children()
                                                        ->scalarNode('name')->end()
                                                        ->scalarNode('method')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('create')
                                    ->prototype('array')
                                        ->children()
                                            ->arrayNode('service')
                                                ->children()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('method')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('delete')
                                    ->prototype('array')
                                        ->children()
                                            ->arrayNode('service')
                                                ->children()
                                                    ->scalarNode('name')->end()
                                                    ->scalarNode('method')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                 ->end()
        ;

        return $treeBuilder;
    }
}
