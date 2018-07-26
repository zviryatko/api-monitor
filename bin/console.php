<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

/** @var \Interop\Container\ContainerInterface $container */
$container = require dirname(__DIR__) . '/config/container.php';
$application = new Application('Log monitor');

$commands = $container->get('config')['console']['commands'];
foreach ($commands as $command) {
  $application->add($container->get($command));
}

$application->run();
