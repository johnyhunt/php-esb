<?php

declare(strict_types=1);

namespace Example;

use ESB\Assembler\DsnInterpreterInterface;
use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\TargetRequestMap;
use ESB\Repository\RouteRepositoryInterface;
use Exception;

use function implode;

class RouteRepository implements RouteRepositoryInterface
{
    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct(private readonly DsnInterpreterInterface $dsnInterpreter)
    {
        $routes = [
            new Route(
                name: 'route_0',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'GET', '/v1/empty'])),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'GET', 'https://catfact.ninja/fact'])),
                toSystemData: new TargetRequestMap(responseFormat: 'json'),
                syncSettings: null,
                postSuccessHandlers: null,
                postErrorHandlers: null,
                customRunner: null
            )
        ];

        foreach ($routes as $route) {
            $this->routes[$route->fromSystemDsn()->dsn()] = $route;
        }
    }

    public function get(string $fromSystemDsn) : Route
    {
        return $this->routes[$fromSystemDsn] ?? throw new Exception('Unknown route');
    }

    public function getByName(string $name) : Route
    {
        foreach ($this->routes as $route) {
            if ($route->name() === $name) {
                return $route;
            }
        }

        throw new Exception(sprintf('%s route wasn`t found', $name));
    }

    public function loadAll() : array
    {
        return $this->routes;
    }

    public function store(Route $route) : void
    {
    }

    public function delete(Route $route) : void
    {
    }
}
