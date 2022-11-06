<?php

declare(strict_types=1);

namespace ESB\Handlers\MessageMiddlewares;

use ESB\DTO\Message;
use ESB\DTO\QueueHandlerOptions;
use ESB\DTO\QueueHandlerResult;
use ESB\Enum\MessageResultEnum;
use ESB\Handlers\QueueMessageHandlerInterface;
use ESB\Handlers\QueueMessageHandlerMiddlewareInterface;
use Throwable;

class ErrorHandlerMiddleware implements QueueMessageHandlerMiddlewareInterface
{

    public function process(Message $message, QueueMessageHandlerInterface $handler) : QueueHandlerResult
    {
        try {
            return $handler->handle($message);
        } catch (Throwable $e) {
            return new QueueHandlerResult(MessageResultEnum::REQUEUE, new QueueHandlerOptions(errorMessage: $e->getMessage()));
        }
    }
}