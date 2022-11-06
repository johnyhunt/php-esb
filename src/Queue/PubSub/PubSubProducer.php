<?php

declare(strict_types=1);

namespace ESB\Queue\PubSub;

use ESB\DTO\Message;
use ESB\Queue\QueueProducerInterface;
use Google\Cloud\PubSub\Topic;

use function json_encode;

class PubSubProducer implements QueueProducerInterface
{
    public function __construct(private readonly Topic $topic)
    {
    }

    public function send(Message $message) : array
    {
        return $this->topic->publish(['data' => json_encode($message)]);
    }
}
