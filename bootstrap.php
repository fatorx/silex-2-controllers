<?php

require __DIR__.'/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

//se for false usa o APC como cache, se for true usa cache em arrays
$isDevMode = true;
$namespace = 'EnterGame';
$configParams = require __DIR__ . '/app/config/doctrine.php';
$paths = [__DIR__ . '/src/' . $namespace . '/Entities'];

$config = Setup::createConfiguration($isDevMode);

$driver = new AnnotationDriver(new AnnotationReader(), $paths);
$config->setMetadataDriverImpl($driver);

AnnotationRegistry::registerFile(
    __DIR__ . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
);
//cria o entityManager
$entityManager = EntityManager::create($configParams['db.options'], $config);