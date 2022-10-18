<?php

use DI\ContainerBuilder;
use TestESB\ContainerConfig;
use ESB\ContainerConfig as RouteConfig;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions((new RouteConfig())(), (new ContainerConfig())());

// Build PHP-DI Container instance
return $containerBuilder->build();
