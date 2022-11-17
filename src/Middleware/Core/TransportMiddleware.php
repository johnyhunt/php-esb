<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Service\AuthServicePool;
use ESB\Service\ClientPool;

class TransportMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly ClientPool $clientPool, private readonly AuthServicePool $authServicePool)
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        if ($authMap = $route->toSystemData()->auth()) {
            $authService = $this->authServicePool->get($authMap->serviceAlias());
            $authService->authenticate($data->targetRequest, $authMap->settings());
        }
        $client = $this->clientPool->get($route->toSystemDsn());

        return $handler->handle(
            $data->withTargetResponse(
                $client->send($route->toSystemDsn(), $data->targetRequest, $route->toSystemData()->responseFormat())
            ),
            $route,
        );
    }
}
