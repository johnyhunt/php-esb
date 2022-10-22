<?php

declare(strict_types=1);

namespace Example;

use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Exception\ESBException;
use ESB\RouteProviderInterface;
use Example\Service\DsnInterpreterInterface;

class RouteProvider implements RouteProviderInterface
{
    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct(private readonly DsnInterpreterInterface $dsnInterpreter)
    {
        $routes = [
            new Route(
                id: 'id_1',
                name: 'route_1',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)('HTTP:GET:/v1/test'),
                fromSystemData: [],
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)('HTTP:POST:google.com'),
            ),
            new Route(
                id: 'id_2',
                name: 'route_2',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)('HTTP:POST:/v1/test-post'),
                fromSystemData: [],
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)('HTTP:POST:google.com'),
            ),
            new Route(
                id: 'id_3',
                name: 'route_3',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)('pubsub:example:test-action'),
                fromSystemData: [],
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)('HTTP:POST:google.com'),
            ),
        ];

        foreach ($routes as $route) {
            $this->routes[$route->fromSystemDsn()->dsn()] = $route;
        }
    }

    public function get(string $key) : Route
    {
        return $this->routes[$key] ?? throw new ESBException('Unknown route');
    }

    public function loadAll() : array
    {
        return $this->routes;
    }
}
