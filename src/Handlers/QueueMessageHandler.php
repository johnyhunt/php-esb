<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\DTO\Message;
use ESB\DTO\QueueHandlerOptions;
use ESB\DTO\QueueHandlerResult;
use ESB\Enum\MessageResultEnum;

class QueueMessageHandler implements QueueMessageHandlerInterface
{
    private QueueMessageHandlerInterface $handler;

    public function __construct(QueueMessageHandlerMiddlewareInterface ...$middlewares)
    {
        // last default handler, if no response returned yet
        $this->handler = new class() implements QueueMessageHandlerInterface {
            public function handle(Message $message) : QueueHandlerResult
            {
                return new QueueHandlerResult(MessageResultEnum::REQUEUE, new QueueHandlerOptions());
            }
        };
        // setup middlewares
        foreach ($middlewares as $middleware) {
            $next = $this->handler;
            $this->handler = new class($middleware, $next) implements QueueMessageHandlerInterface
            {
                public function __construct(private readonly QueueMessageHandlerMiddlewareInterface $middleware, private readonly QueueMessageHandlerInterface $next)
                {
                }

                public function handle(Message $message) : QueueHandlerResult
                {
                    return $this->middleware->process($message, $this->next);
                }
            };
        }
    }

    public function handle(Message $message) : QueueHandlerResult
    {
        return $this->handler->handle($message);
    }
}
