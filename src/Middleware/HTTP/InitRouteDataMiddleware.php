<?php

namespace ESB\Middleware\HTTP;

use ESB\DTO\IncomeData;
use ESB\DTO\RouteData;
use ESB\Entity\Route;
use ESB\Repository\RouteRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InitRouteDataMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeProvider,
        private readonly string                   $basePath = '/middleware'
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = substr($request->getUri()->getPath(), strlen($this->basePath));
        $dsn  = implode(';', ['HTTP', strtoupper($request->getMethod()), $path]);

        $routeEntity = $this->routeProvider->get($dsn);
        $incomeData  = new IncomeData(
            $request->getHeaders(),
            $request->getAttributes(),
            $request->getParsedBody() ?: $request->getQueryParams()
        );

        return $handler->handle(
            $request
                ->withAttribute(Route::class, $routeEntity)
                ->withAttribute(RouteData::class, new RouteData($incomeData))
        );
    }
}
