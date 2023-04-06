<?php

declare(strict_types=1);

namespace ESB;

use Assert\Assertion;
use ESB\Entity\VO\HttpDSN;
use ESB\Handlers\HTTP\ESBHandlerInterface;
use ESB\Handlers\HTTP\RouteCRUDHandler;
use ESB\Handlers\HTTP\RouteListHandler;
use ESB\Middleware\HTTP\InitRouteDataMiddleware;
use ESB\Repository\RouteRepositoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
            $group->options('{routes:.*}', function (RequestInterface $request, ResponseInterface $response, array $args) {
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
            $group->options('{routes:.*}', function (RequestInterface $request, ResponseInterface $response, array $args) {
                return $response;
            });
            foreach ($routes as $route) {
                $routeDsn = $route->fromSystemDsn();
                if (! $routeDsn instanceof HttpDSN) {
                    continue;
                }
                $group->map([$routeDsn->method], $routeDsn->path, ESBHandlerInterface::class);
            }
        })->add(new InitRouteDataMiddleware($this->provider, $this->basePath));
    }

    protected function setupMiddlewares(App $app) : void
    {
        $app->addRoutingMiddleware();

        $app->addBodyParsingMiddleware();
    }
}
