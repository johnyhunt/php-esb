<?php

declare(strict_types=1);

namespace ESB\Service;

use Opsway\ESB\Handlers\ESBHandler;
use Opsway\ESB\Service\RouteProviderInterface;
use Opsway\ESB\Service\ServerAppSetup as CoreSetUp;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class ServerAppSetup extends CoreSetUp
{
    public function __construct(private readonly RouteProviderInterface $provider)
    {
        parent::__construct($this->provider);
    }

    protected function setupRoutes(App $app): void
    {
        $routes = $this->provider->loadAll();
        $app->group('/middleware1', function (RouteCollectorProxy $group) use ($routes) {
            foreach ($routes as $route) {
                if (! $method = $route->fromSystemTransportMethod) {
                    continue;
                }
                $group->map([$method->value], '/' . $route->key(), ESBHandler::class);
            }
        });
    }
}
