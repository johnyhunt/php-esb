<?php

declare(strict_types=1);

namespace Example\Queue\PubSub;

use ESB\Queue\QueueConfigInterface;

class PubSubConfig implements QueueConfigInterface
{
    public function __construct(public readonly string $topic, public readonly string $subscription, public readonly array $options)
    {
    }
}
