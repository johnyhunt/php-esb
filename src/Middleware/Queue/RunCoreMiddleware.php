<?php

declare(strict_types=1);

namespace ESB\Middleware\Queue;

use ESB\DTO\IncomeData;
use ESB\DTO\Message\Envelope;
use ESB\DTO\Message\ReceiveStamp;
use ESB\DTO\ProcessingData;
use ESB\DTO\QueueHandlerOptions;
use ESB\DTO\QueueHandlerResult;
use ESB\Enum\MessageResultEnum;
use ESB\Exception\ESBException;
use ESB\Exception\StopProcessingException;
use ESB\Handlers\QueueMessageHandlerInterface;
use ESB\Handlers\QueueMessageHandlerMiddlewareInterface;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Service\CoreRunnersPool;
use function json_decode;

class RunCoreMiddleware implements QueueMessageHandlerMiddlewareInterface
{
    public function __construct(private readonly RouteRepositoryInterface $routeRepository, private readonly CoreRunnersPool $runnersPool)
    {
    }

    public function process(Envelope $envelope, QueueMessageHandlerInterface $handler) : QueueHandlerResult
    {
        $body = json_decode($envelope->message->body, true);
        /** json in message is corrupted */
        if ($envelope->message->body && ! $body) {
            throw new ESBException(sprintf('Message body corrupted = %s', $envelope->message->body));
        }
        $processingData = new ProcessingData(
            new IncomeData(headers: $envelope->message->attributes, params: [], body: $body)
        );

        $receivedStamp = $envelope->getStamp(ReceiveStamp::class);
        if (! $receivedStamp instanceof ReceiveStamp) {
            throw new StopProcessingException('Envelope in RunCoreMiddleware expected ReceiveStamp been set');
        }
        $route = $this->routeRepository->get($receivedStamp->routingDsn->dsn());

        $this->runnersPool->get($route->customRunner())->runCore($processingData, $route);

        return new QueueHandlerResult(MessageResultEnum::ACK, new QueueHandlerOptions());
    }
}
