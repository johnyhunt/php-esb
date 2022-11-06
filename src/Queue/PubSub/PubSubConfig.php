<?php

declare(strict_types=1);

namespace ESB\Queue\PubSub;

use ESB\Entity\VO\PubSubDSN;
use ESB\Queue\QueueConfigInterface;

class PubSubConfig implements QueueConfigInterface
{
    public function __construct(public readonly string $topic, public readonly string $subscription, public readonly array $options)
    {
    }

    public function buildDsn(string $action) : PubSubDSN
    {
        return new PubSubDSN('pubsub', $this->topic, $this->subscription, $action);
    }
}
