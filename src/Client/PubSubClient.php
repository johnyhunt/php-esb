<?php

declare(strict_types=1);

namespace ESB\Client;

use ESB\DTO\Message;
use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\PubSubDSN;
use ESB\Exception\ESBException;
use ESB\Queue\PubSub\PubSubConfig;
use ESB\Queue\PubSub\PubSubFactory;

class PubSubClient implements EsbClientInterface
{
    public function __construct(private readonly PubSubFactory $factory)
    {
    }

    public function send(AbstractDSN $dsn, TargetRequest $targetRequest): TargetResponse
    {
        if (! $dsn instanceof PubSubDSN) {
            throw new ESBException('PubSubClient expects dsn been PubSubDSN instance');
        }
        $producer = $this->factory->producer(new PubSubConfig($dsn->topic, "$dsn->topic.sub", []));
        $result   = $producer->send(new Message($targetRequest->body, $dsn->action, $targetRequest->headers));

        return new TargetResponse($result, 200, []);
    }

    public function dsnMatchClass() : string
    {
        return PubSubDSN::class;
    }
}
