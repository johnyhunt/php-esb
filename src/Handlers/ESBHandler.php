<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\Response\ESBJsonResponse;
use ESB\Service\RouteProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function date;
use function implode;
use function strlen;
use function substr;

class ESBHandler implements EsbHandlerInterface
{
    public function __construct(
        private readonly RouteProviderInterface $routeProvider,
        private readonly ESBCoreHandlerInterface $coreHandler,
        private readonly string $basePath,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $path = substr($request->getRequestTarget(), strlen($this->basePath));
        $dsn  = implode(':', ['HTTP', strtoupper($request->getMethod()), $path]);

        $routeEntity = $this->routeProvider->get($dsn);
        $incomeData  = [
            'headers' => $request->getHeaders(),
            'params'  => $request->getAttributes(),
            'body'    => $request->getParsedBody() ?: $request->getQueryParams(),
        ];

        $this->coreHandler->run($incomeData, $routeEntity);

        return new ESBJsonResponse(
            [
                'date' => date('Y-m-s H:i:s'),
                'data' => $incomeData,
            ]
        );
    }
}
