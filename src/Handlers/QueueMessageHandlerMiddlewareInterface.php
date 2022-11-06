<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\DTO\Message;
use ESB\DTO\QueueHandlerResult;

interface QueueMessageHandlerMiddlewareInterface
{
    public function process(Message $message, QueueMessageHandlerInterface $handler) : QueueHandlerResult;
}
