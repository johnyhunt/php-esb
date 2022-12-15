<?php

declare(strict_types=1);

namespace ESB;

use Assert\Assertion;
use ESB\Entity\VO\ServerDSN;
use ESB\Handlers\HTTP\ESBHandler;
use ESB\Handlers\HTTP\RouteCRUDHandler;
use ESB\Handlers\HTTP\RouteListHandler;
use ESB\Middleware\HTTP\InitRouteDataMiddleware;
use ESB\Repository\RouteRepositoryInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use function preg_match;

class ServerAppSetup
{
    public function __construct(private readonly RouteRepositoryInterface $provider, private readonly string $basePath = '/middleware')
    {
        Assertion::true(! ! preg_match('/(\/\w+(\/)?)+/', $this->basePath), 'ServerAppSetup: basePath expecting been uri-path');
    }

    public function __invoke(App $app) : void
    {
        $this->setupRoutes($app);
        $this->setupCrudRoutes($app);
        $this->setupMiddlewares($app);
    }

    protected function setupCrudRoutes(App $app) : void
    {

        $app->group('/route', function (RouteCollectorProxy $group) {
            $group->options('{routes:.*}', function ($request, $response, $args) {
                return $response;
            });
            $group->map(['POST', 'PUT'], '', RouteCRUDHandler::class);
            $group->delete('/{name}', RouteCRUDHandler::class);
            $group->get('/{name}', RouteCRUDHandler::class);

            $group->get('', RouteListHandler::class);
        });
    }

    protected function setupRoutes(App $app) : void
    {
        $routes = $this->provider->loadAll();
        $app->group($this->basePath, function (RouteCollectorProxy $group) use ($routes) {
            foreach ($routes as $route) {
                if (! $route->fromSystemDsn() instanceof ServerDSN) {
                    continue;
                }
                $group->map([$route->fromSystemDsn()->method], $route->fromSystemDsn()->path, ESBHandler::class);
            }
        })->add(new InitRouteDataMiddleware($this->provider, $this->basePath));
    }

    protected function setupMiddlewares(App $app) : void
    {
        $app->addRoutingMiddleware();

        $app->addBodyParsingMiddleware();
    }
}
