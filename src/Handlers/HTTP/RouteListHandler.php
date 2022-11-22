<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use ESB\Repository\RouteRepositoryInterface;
use ESB\Response\ESBJsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouteListHandler
{
    public function __construct(private readonly RouteRepositoryInterface $routeRepository)
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $routes = $this->routeRepository->loadAll();

        return new ESBJsonResponse($routes);
    }
}
