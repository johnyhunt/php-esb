<?php

declare(strict_types=1);

namespace ESB\Middleware\Queue;

use ESB\DTO\IncomeData;
use ESB\DTO\Message;
use ESB\DTO\ProcessingData;
use ESB\DTO\QueueHandlerOptions;
use ESB\DTO\QueueHandlerResult;
use ESB\Enum\MessageResultEnum;
use ESB\Exception\ESBException;
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

    public function process(Message $message, QueueMessageHandlerInterface $handler) : QueueHandlerResult
    {
        $body = json_decode($message->body, true);
        /** json in message is corrupted */
        if ($message->body && ! $body) {
            throw new ESBException(sprintf('Message body corrupted = %s', $message->body));
        }
        $processingData = new ProcessingData(
            new IncomeData(headers: $message->attributes, params: [], body: $body)
        );

        $route = $this->routeRepository->get($message->xroute);

        $this->runnersPool->get($route->customRunner())->runCore($processingData, $route);

        return new QueueHandlerResult(MessageResultEnum::ACK, new QueueHandlerOptions());
    }
}
