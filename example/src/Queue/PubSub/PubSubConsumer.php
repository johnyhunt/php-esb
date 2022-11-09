<?php

declare(strict_types=1);

namespace Example\Queue\PubSub;

use ESB\DTO\Message;
use ESB\Exception\ESBException;
use ESB\Queue\QueueConsumerInterface;
use Google\Cloud\PubSub\Message as PubSubMessage;
use Google\Cloud\PubSub\Subscription;
use function current;

class PubSubConsumer implements QueueConsumerInterface
{
    public function __construct(private readonly Subscription $subscription)
    {
    }

    public function receive(int $timeout = 0) : ?Message
    {
        $messages = $this->subscription->pull([
            'maxMessages' => 1,
            'returnImmediately' => true,
        ]);

        if ($messages && $message = current($messages)) {
            return (Message::deserialize($message->data()))->injectNativeMessage($message);
        }

        return null;
    }

    public function acknowledge(Message $message) : void
    {
        $nativeMessage = $message->nativeMessage();
        if (! $nativeMessage instanceof PubSubMessage) {
            throw new ESBException('PubSubConsumer::acknowledge expects native message been instance of PubSubMessage');
        }
        $this->subscription->acknowledge($nativeMessage);
    }

    public function reject(Message $message) : void
    {
        $this->acknowledge($message);
    }

    public function requeue(Message $message, int $delay = 0) : void
    {
        $nativeMessage = $message->nativeMessage();
        if (! $nativeMessage instanceof PubSubMessage) {
            throw new ESBException('PubSubConsumer::acknowledge expects native message been instance of PubSubMessage');
        }
        $this->subscription->modifyAckDeadline($nativeMessage, $delay);
    }
}
