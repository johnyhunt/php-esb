<?php

declare(strict_types=1);

use ESB\Response\ESBJsonResponse;
use ESB\ServerAppSetup;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/bootstrap.php';

AppFactory::setContainer($container);
// Create App instance
$app            = AppFactory::create();
$serverAppSetup = $container->get(ServerAppSetup::class)($app);

// Add root route
$app->get('/', function (ServerRequest $request, Response $response, $args) {
    return new ESBJsonResponse(['status' => 'ok']);
});

$app->map(['GET', 'POST', 'PUT', 'PATCH'], '/ping', function (ServerRequest $request, Response $response, $args) {
    return new ESBJsonResponse(['status' => 'ok']);
});

$app->run();
