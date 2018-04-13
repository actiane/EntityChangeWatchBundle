<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures;

use Actiane\EntityChangeWatchBundle\EntityChangeWatchBundle;
use Actiane\EntityChangeWatchBundle\Tests\TestContainerPass;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 * @package Actiane\EntityChangeWatchBundle\Tests\App
 */
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new DoctrineBundle(),
            new EntityChangeWatchBundle(),
        ];
    }
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yaml');
    }

    protected function build(ContainerBuilder $container)
    {
        parent::build($container);
        if ($this->getEnvironment() === 'test') {
            $container->addCompilerPass(new TestContainerPass(), PassConfig::TYPE_OPTIMIZE);
        }
    }


}
