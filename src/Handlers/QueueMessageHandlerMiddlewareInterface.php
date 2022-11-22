<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\DTO\Message\Envelope;
use ESB\DTO\QueueHandlerResult;

interface QueueMessageHandlerMiddlewareInterface
{
    public function process(Envelope $envelope, QueueMessageHandlerInterface $handler) : QueueHandlerResult;
}
