<?php

declare(strict_types=1);

namespace ESB\Middleware\Queue;

use ESB\DTO\Message\Envelope;
use ESB\DTO\QueueHandlerOptions;
use ESB\DTO\QueueHandlerResult;
use ESB\Enum\MessageResultEnum;
use ESB\Handlers\QueueMessageHandlerInterface;
use ESB\Handlers\QueueMessageHandlerMiddlewareInterface;
use Throwable;

class ErrorHandlerMiddleware implements QueueMessageHandlerMiddlewareInterface
{

    public function process(Envelope $envelope, QueueMessageHandlerInterface $handler) : QueueHandlerResult
    {
        try {
            return $handler->handle($envelope);
        } catch (Throwable $e) {
            return new QueueHandlerResult(MessageResultEnum::REQUEUE, new QueueHandlerOptions(errorMessage: $e->getMessage()));
        }
    }
}
