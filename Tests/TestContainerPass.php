<?php


namespace Actiane\EntityChangeWatchBundle\Tests;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Makes enumerated private services that needs to be tested public
 * so they can be fetched from the container without a deprecation warning.
 *
 * @see https://github.com/symfony/symfony-docs/issues/8097
 * @see https://github.com/symfony/symfony/issues/24543
 */
class TestContainerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            $definition->setPublic(true);
        }
    }
}