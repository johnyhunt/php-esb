<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Repository\CommunicationLogInterface;
use ESB\Service\AuthServicePool;
use ESB\Service\ClientPool;

use function filter_var;
use function getenv;

use const FILTER_VALIDATE_BOOLEAN;

class TransportMiddleware implements ESBMiddlewareInterface
{
    public function __construct(
        private readonly ClientPool $clientPool,
        private readonly AuthServicePool $authServicePool,
        private readonly ?CommunicationLogInterface $communicationLog = null,
    ) {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        if ($authMap = $route->toSystemData()->auth()) {
            $authService = $this->authServicePool->get($authMap->serviceAlias());
            $authService->authenticate($data->targetRequest(), $authMap->settings());
        }
        $client     = $this->clientPool->get($route->toSystemDsn());
        $resultData = $handler->handle(
            $data->withTargetResponse(
                $client->send($route->toSystemDsn(), $data->targetRequest(), $route->toSystemData()->responseFormat())
            ),
            $route,
        );

        if (filter_var(getenv('PHPESB_RUN_COMMUNICATION_LOG'), FILTER_VALIDATE_BOOLEAN)) {
            $this->communicationLog?->log($route, $resultData);
        }

        return $resultData;
    }
}
