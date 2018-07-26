<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require dirname(__FILE__) . '/../vendor/autoload.php';
/** @var \Interop\Container\ContainerInterface $container */
$container = require __DIR__ . '/container.php';
/** @var \Doctrine\ORM\EntityManager $em */
$em = $container->get(EntityManager::class);
return ConsoleRunner::createHelperSet($em);
