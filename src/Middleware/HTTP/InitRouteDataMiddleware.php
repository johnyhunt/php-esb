<?php

namespace ESB\Middleware\HTTP;

use ESB\DTO\IncomeData;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Entity\VO\ServerDSN;
use ESB\Repository\RouteRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

use function strtoupper;

class InitRouteDataMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly RouteRepositoryInterface $routeProvider, private readonly string $basePath) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $path = substr($request->getUri()->getPath(), strlen($this->basePath));
        $dsn  = new ServerDSN(strtoupper($request->getMethod()), $path);

        $routeEntity = $this->routeProvider->get($dsn->dsn());
        $incomeData  = new IncomeData(
            $request->getHeaders(),
            $request->getAttributes(),
            (array) $request->getParsedBody() ?: $request->getQueryParams(),
            Uuid::uuid4()->toString(),
        );

        return $handler->handle(
            $request
                ->withAttribute(Route::class, $routeEntity)
                ->withAttribute(ProcessingData::class, new ProcessingData($incomeData))
        );
    }
}
