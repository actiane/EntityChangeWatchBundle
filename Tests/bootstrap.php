<?php

use Symfony\Component\Cache\Adapter\ArrayAdapter;

$filesystem = new \Symfony\Component\Filesystem\Filesystem();
$filesystem->remove(__DIR__.'/Fixtures/cache/test');

if (!is_file($loaderFile = __DIR__.'/../vendor/autoload.php') && !is_file(
        $loaderFile = __DIR__.'/../../../../../../vendor/autoload.php'
    )) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

$loader = require $loaderFile;

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$reader = new \Doctrine\Common\Annotations\AnnotationReader();
$reader = new \Doctrine\Common\Annotations\PsrCachedReader($reader, new ArrayAdapter);
$_ENV['annotation_reader'] = $reader;
