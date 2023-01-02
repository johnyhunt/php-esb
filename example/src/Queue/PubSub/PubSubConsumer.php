<?php

declare(strict_types=1);

namespace Example\Queue\PubSub;

use ESB\DTO\Message\Envelope;
use ESB\DTO\Message\Message;
use ESB\DTO\Message\ReceiveStamp;
use ESB\Queue\QueueConfigInterface;
use ESB\Queue\QueueConsumerInterface;
use Example\Entity\VO\PubSubDSN;
use Exception;
use Google\Cloud\PubSub\Message as PubSubMessage;
use Google\Cloud\PubSub\Subscription;

use function current;

class PubSubConsumer implements QueueConsumerInterface
{
    public function __construct(private readonly Subscription $subscription)
    {
    }

    public function receive(QueueConfigInterface|PubSubConfig $config) : ?Envelope
    {
        $messages = $this->subscription->pull([
            'maxMessages'       => 1,
            'returnImmediately' => true,
        ]);

        if ($messages && $nativeMessage = current($messages)) {
            $message = Message::deserialize($nativeMessage->data());
            $dsn     = new PubSubDSN($config->topic, $config->subscription, $message->action);

            return new Envelope($message, new ReceiveStamp($dsn, $nativeMessage));
        }

        return null;
    }

    public function acknowledge(Envelope $envelope) : void
    {
        /** @psalm-var ReceiveStamp|null $receivedStamp */
        $receivedStamp = $envelope->getStamp(ReceiveStamp::class);
        $nativeMessage = $receivedStamp?->nativeMessage;
        if (! $nativeMessage instanceof PubSubMessage) {
            throw new Exception('PubSubConsumer::acknowledge expects native message been instance of PubSubMessage');
        }
        $this->subscription->acknowledge($nativeMessage);
    }

    public function reject(Envelope $envelope) : void
    {
        $this->acknowledge($envelope);
    }

    public function requeue(Envelope $envelope, int $delay = 0) : void
    {
        /** @psalm-var ReceiveStamp|null $receivedStamp */
        $receivedStamp = $envelope->getStamp(ReceiveStamp::class);
        $nativeMessage = $receivedStamp?->nativeMessage;
        if (! $nativeMessage instanceof PubSubMessage) {
            throw new Exception('PubSubConsumer::acknowledge expects native message been instance of PubSubMessage');
        }
        $this->subscription->modifyAckDeadline($nativeMessage, $delay);
    }
}
