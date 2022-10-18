<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require_once __DIR__ . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
//
//// Build PHP-DI Container instance
//$container = $containerBuilder->build();