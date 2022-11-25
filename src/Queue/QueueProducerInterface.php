<?php

declare(strict_types=1);

namespace ESB\Queue;

use ESB\DTO\Message\Envelope;

interface QueueProducerInterface
{
    public function send(Envelope $envelope) : mixed;
}
