<?php

declare(strict_types=1);

namespace Opsway\ESB\Service;

use Opsway\ESB\Entity\HttpMethod;
use Opsway\ESB\Entity\Route;
use Opsway\ESB\Entity\SystemTransport;
use Opsway\ESB\Exception\ESBException;
use Ramsey\Uuid\Uuid;

class RouteProvider implements RouteProviderInterface
{
    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct()
    {
        $routes = [
            new Route(
                Uuid::uuid4()->toString(),
                'dispatch-box',
                '',
                'boodmo',
                'sap',
                SystemTransport::HTTP,
                SystemTransport::HTTP,
                 fromSystemTransportMethod: HttpMethod::POST,
            ),
            new Route(
                Uuid::uuid4()->toString(),
                'dispatch-order',
                '',
                'boodmo',
                'sap',
                SystemTransport::ASYNC,
                SystemTransport::HTTP,
            ),
            new Route(
                Uuid::uuid4()->toString(),
                'create-invoice',
                '',
                'sap',
                'boodmo',
                SystemTransport::HTTP,
                SystemTransport::HTTP,
                fromSystemTransportMethod: HttpMethod::POST,
            ),
        ];

        foreach ($routes as $route) {
            $this->routes[$route->key()] = $route;
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
