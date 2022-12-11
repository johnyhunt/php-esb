<?php

declare(strict_types=1);

namespace Example\Clients;

use ESB\Client\EsbClientInterface;
use ESB\DTO\Message\Envelope;
use ESB\DTO\Message\Message;
use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;
use ESB\Exception\ESBException;
use Example\Entity\VO\PubSubDSN;
use Example\Queue\PubSub\PubSubConfig;
use Example\Queue\PubSub\PubSubFactory;
use function microtime;

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
        $start    = microtime();
        $result   = $producer->send(
            new Envelope(new Message($targetRequest->body, $dsn->action, $targetRequest->headers))
        );
        $spentTime = microtime() - $start;

        return new TargetResponse($result, (int) $spentTime);
    }

    public function dsnMatchClass() : string
    {
        return PubSubDSN::class;
    }
}
