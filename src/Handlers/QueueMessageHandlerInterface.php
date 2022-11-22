<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\DTO\Message\Envelope;
use ESB\DTO\QueueHandlerResult;

interface QueueMessageHandlerInterface
{
    public function handle(Envelope $envelope) : QueueHandlerResult;
}
