<?php

namespace Actiane\EntityChangeWatchBundle;

use Actiane\EntityChangeWatchBundle\DependencyInjection\Compiler\CallbackServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EntityChangeWatchBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CallbackServiceCompilerPass());
    }
}
