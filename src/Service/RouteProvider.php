<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Entity\Route;
use ESB\Exception\ESBException;
use Ramsey\Uuid\Uuid;
use function var_dump;

class RouteProvider implements RouteProviderInterface
{
    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct(private readonly DsnInterpreterInterface $dsnInterpreter)
    {
        $routes = [
            new Route(
                Uuid::uuid4()->toString(),
                'dispatch-box',
                'sap',
                ($this->dsnInterpreter)('HTTP:POST:/v1/boodmo/sap/dispatch-box'),
                [],
            ),
            new Route(
                Uuid::uuid4()->toString(),
                'dispatch-order',
                'e-Invoice',
                ($this->dsnInterpreter)('pubsub:edocument:generateDocument'),
                [],
            ),
            new Route(
                Uuid::uuid4()->toString(),
                'create-invoice',
                'sap',
                ($this->dsnInterpreter)('HTTP:POST:/v1/boodmo/sap/create-invoice'),
                []
            ),
        ];

        foreach ($routes as $route) {
            $this->routes[$route->fromSystemDsn->dsn()] = $route;
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
