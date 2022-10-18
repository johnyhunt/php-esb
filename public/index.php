<?php

declare(strict_types=1);

use ESB\Service\ServerAppSetupInterface;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/bootstrap.php';

AppFactory::setContainer($container);
// Create App instance
$app            = AppFactory::create();
$serverAppSetup = $container->get(ServerAppSetupInterface::class)($app);

$app->run();
