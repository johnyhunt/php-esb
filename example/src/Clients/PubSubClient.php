<?php

declare(strict_types=1);

namespace Example\Clients;

use ESB\Client\EsbClientInterface;
use ESB\DTO\Message;
use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\PubSubDSN;
use ESB\Exception\ESBException;
use Example\Queue\PubSub\PubSubConfig;
use Example\Queue\PubSub\PubSubFactory;

class PubSubClient implements EsbClientInterface
{
    public function __construct(private readonly PubSubFactory $factory)
    {
    }

    public function send(AbstractDSN $dsn, TargetRequest $targetRequest, string $responseFormat): TargetResponse
    {
        if (! $dsn instanceof PubSubDSN) {
            throw new ESBException('PubSubClient expects dsn been PubSubDSN instance');
        }
        $producer = $this->factory->producer(new PubSubConfig($dsn->topic, $dsn->subscription, []));
        $result   = $producer->send(new Message($targetRequest->body, $dsn->xroute, $targetRequest->headers));

        return new TargetResponse($result, 200, []);
    }

    public function dsnMatchClass() : string
    {
        return PubSubDSN::class;
    }
}
