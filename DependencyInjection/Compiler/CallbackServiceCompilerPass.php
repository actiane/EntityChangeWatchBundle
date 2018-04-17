<?php


namespace Actiane\EntityChangeWatchBundle\DependencyInjection\Compiler;

use Actiane\EntityChangeWatchBundle\DependencyInjection\ServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CallbackServiceCompilerPass
 * @package Actiane\EntityChangeWatchBundle\DependencyInjection\Compiler
 */
class CallbackServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('actiane.entitywatch.callback_locator');

        $taggedServices = $container->findTaggedServiceIds('actiane.entitychangewatch.callback');

        $callbacks = [];
        foreach ($taggedServices as $id => $tags) {
            $callbacks[$id] = new Reference($id);
        }

        $definition->addArgument($callbacks);
    }
}
