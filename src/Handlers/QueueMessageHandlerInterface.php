<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\DTO\Message;
use ESB\DTO\QueueHandlerResult;

interface QueueMessageHandlerInterface
{
    public function handle(Message $message) : QueueHandlerResult;
}
