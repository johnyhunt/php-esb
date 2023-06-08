<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Service\AuthServicePool;
use ESB\Service\ClientPool;
use ESB\Service\DynamicDsnParserInterface;
use ESB\Service\DynamicPropertiesFetcherInterface;

class TransportMiddleware implements ESBMiddlewareInterface
{
    public function __construct(
        private readonly ClientPool $clientPool,
        private readonly AuthServicePool $authServicePool,
        private readonly DynamicPropertiesFetcherInterface $dynamicPropertiesFetcher,
        private readonly DynamicDsnParserInterface $dynamicDsnParser,
    ) {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        if ($authMap = $route->toSystemData()->auth()) {
            $authService = $this->authServicePool->get($authMap->serviceAlias());
            $authService->authenticate($data->targetRequest(), ($this->dynamicPropertiesFetcher)($data->incomeData, $authMap->settings()));
        }
        $client     = $this->clientPool->get($route->toSystemDsn());
        $requestDsn = ($this->dynamicDsnParser)($data->incomeData, $route->toSystemDsn());
        $resultData = $handler->handle(
            $data->withTargetResponse(
                $client->send($requestDsn, $data->targetRequest(), $route->toSystem(), $route->toSystemData()->responseFormat())
            ),
            $route,
        );

        return $resultData;
    }
}
