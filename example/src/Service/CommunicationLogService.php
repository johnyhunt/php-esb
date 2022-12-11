<?php

declare(strict_types=1);

namespace Example\Service;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Repository\CommunicationLogInterface;
use Google\Cloud\PubSub\PubSubClient;

use function json_encode;

class CommunicationLogService implements CommunicationLogInterface
{
    private const LOG_TOPIC = 'esb.log';

    public function __construct(private readonly PubSubClient $client)
    {
    }

    public function log(Route $route, ProcessingData $processingData) : void
    {
        /** TODO topic should exist */
        $topic = $this->client->topic(self::LOG_TOPIC);
        $topic->publish(
            [
                'data' => [
                    'route'         => $route->name(),
                    'client'        => $route->toSystemDsn()->client ?? null,
                    'method'        => $route->toSystemDsn()->method ?? null,
                    'topic'         => $route->toSystemDsn()->topic ?? null,
                    'action'        => $route->toSystemDsn()->action ?? null,
                    'path'          => $route->toSystemDsn()->path ?? null,
                    'fromSystem'    => $route->fromSystem()->code(),
                    'toSystem'      => $route->toSystem()->code(),
                    'requets'       => json_encode($processingData->targetRequest()),
                    'response'      => json_encode($processingData->targetResponse->content),
                    'response_code' => $processingData->targetResponse->statusCode,
                    'response_time' => $processingData->targetResponse->responseTime,
                ],
            ]
        );
    }
}
