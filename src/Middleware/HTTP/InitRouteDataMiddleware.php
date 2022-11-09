<?php

namespace ESB\Middleware\HTTP;

use ESB\DTO\IncomeData;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Exception\ESBException;
use ESB\Repository\RouteRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InitRouteDataMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly RouteRepositoryInterface $routeProvider) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $xRoute = $request->getHeader('xroute')[0] ?? throw new ESBException('xroute header required in InitRouteDataMiddleware');

        $routeEntity = $this->routeProvider->get($xRoute);
        $incomeData  = new IncomeData(
            $request->getHeaders(),
            $request->getAttributes(),
            $request->getParsedBody() ?: $request->getQueryParams()
        );

        return $handler->handle(
            $request
                ->withAttribute(Route::class, $routeEntity)
                ->withAttribute(ProcessingData::class, new ProcessingData($incomeData))
        );
    }
}
