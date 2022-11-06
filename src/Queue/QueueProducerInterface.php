<?php

declare(strict_types=1);

namespace ESB\Queue;

use ESB\DTO\Message;

interface QueueProducerInterface
{
    public function send(Message $message) : mixed;
}