<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->registerNamespaces(array(
    // ...
    'Mandango\MandangoBundle' => __DIR__.'/../vendor/bundles',
    'Mandango\Mondator'       => __DIR__.'/../vendor/mondator/src',
    'Mandango'                => __DIR__.'/../vendor/mandango/src',
    'Model'                   => __DIR__.'/../src/',
    // ...
));

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
