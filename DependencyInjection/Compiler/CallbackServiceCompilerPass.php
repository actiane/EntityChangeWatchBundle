<?php


namespace Actiane\EntityChangeWatchBundle\DependencyInjection\Compiler;

use Actiane\EntityChangeWatchBundle\Generator\CallableGenerator;
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
        $definition = $container->findDefinition(CallableGenerator::class);

        $taggedServices = $container->findTaggedServiceIds('actiane.entitychangewatch.callback');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addCallback', [$id, new Reference($id)]);
        }
    }
}
