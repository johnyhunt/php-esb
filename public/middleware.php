<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {

    $app->addRoutingMiddleware();

    $app->addBodyParsingMiddleware();

    $app->add(ErrorMiddleware::class);
};
