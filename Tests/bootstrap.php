<?php declare(strict_types = 1);

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\PsrCachedReader;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Filesystem\Filesystem;

$filesystem = new Filesystem();
$filesystem->remove(__DIR__.'/Fixtures/cache/test');

if (!is_file($loaderFile = __DIR__.'/../vendor/autoload.php')
    && !is_file(
        $loaderFile = __DIR__.'/../../../../../../vendor/autoload.php'
    )
) {
    throw new LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $loaderFile;

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$reader = new AnnotationReader();
$reader = new PsrCachedReader($reader, new ArrayAdapter);
$_ENV['annotation_reader'] = $reader;
