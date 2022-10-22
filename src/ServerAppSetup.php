<?php

declare(strict_types=1);

namespace ESB;

use Assert\Assertion;
use ESB\Entity\VO\ServerDSN;
use ESB\Handlers\ESBHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use function preg_match;

class ServerAppSetup
{
    public function __construct(private readonly RouteProviderInterface $provider, private readonly string $basePath)
    {
        /** TODO fix regular expression to prevent last / been as true pattern */
        Assertion::true(! ! preg_match('/(\/\w+(\/)?)+/', $this->basePath), 'ServerAppSetup: basePath expecting been uri-path');
    }

    public function __invoke(App $app) : void
    {
        $this->setupRoutes($app);
        $this->setupMiddlewares($app);
    }

    protected function setupRoutes(App $app) : void
    {
        $routes = $this->provider->loadAll();
        $app->group($this->basePath, function (RouteCollectorProxy $group) use ($routes) {
            foreach ($routes as $route) {
                if (! $route->fromSystemDsn() instanceof ServerDSN) {
                    continue;
                }
                $group->map([$route->fromSystemDsn()->method], $route->fromSystemDsn()->path, ESBHandlerInterface::class);
            }
        });
    }

    protected function setupMiddlewares(App $app) : void
    {
        $app->addRoutingMiddleware();
        $app->addBodyParsingMiddleware();
    }
}
