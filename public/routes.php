<?php

use Opsway\ESB\Handlers\ESBHandler;
use Opsway\ESB\Service\RouteProviderInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app, RouteProviderInterface $provider) {
    $app->group('/middleware', function (RouteCollectorProxy $group) use ($provider) {
        foreach ($provider->loadAll() as $route) {
            if (! $method = $route->fromSystemTransportMethod) {
                continue;
            }
            $group->map([$method], $route->key(), ESBHandler::class);
        }
    });
};