<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CallbackServiceCompilerPass
 */
class CallbackServiceCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
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
