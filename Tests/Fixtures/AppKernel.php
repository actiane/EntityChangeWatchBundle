<?php declare(strict_types=1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures;

use Actiane\EntityChangeWatchBundle\EntityChangeWatchBundle;
use Actiane\EntityChangeWatchBundle\Tests\TestContainerPass;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new EntityChangeWatchBundle(),
        ];
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TestContainerPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
