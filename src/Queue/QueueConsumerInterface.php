<?php

declare(strict_types=1);

namespace ESB\Queue;

use ESB\DTO\Message\Envelope;

interface QueueConsumerInterface
{
    public function receive(QueueConfigInterface $config) : ?Envelope;

    public function acknowledge(Envelope $envelope) : void;

    public function reject(Envelope $envelope) : void;

    public function requeue(Envelope $envelope, int $delay = 0) : void;
}
