<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Response\ESBJsonResponse;
use ESB\Service\CoreRunnersPool;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ESBHandler implements ESBHandlerInterface
{
    public function __construct(private readonly CoreRunnersPool $runnersPool)
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        /** @psalm-var Route $route */
        $route = $request->getAttribute(Route::class);
        /** @psalm-var ProcessingData $routeData */
        $routeData = $request->getAttribute(ProcessingData::class);

        $result = $this->runnersPool->get($route->customRunner())->runCore($routeData, $route);

        return new ESBJsonResponse(body: $result->targetResponse()->content, statusCode: $result->targetResponse()->statusCode);
    }
}
