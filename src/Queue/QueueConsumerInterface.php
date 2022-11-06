<?php

declare(strict_types=1);

namespace ESB\Queue;

use ESB\DTO\Message;

interface QueueConsumerInterface
{
    public function receive(int $timeout = 0) : ?Message;

    public function acknowledge(Message $message) : void;

    public function reject(Message $message) : void;

    public function requeue(Message $message, int $delay = 0) : void;
}