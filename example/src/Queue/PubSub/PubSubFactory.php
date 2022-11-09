<?php

declare(strict_types=1);

namespace Example\Queue\PubSub;

use ESB\Exception\ESBException;
use ESB\Queue\QueueConfigInterface;
use ESB\Queue\QueueFactoryInterface;
use Google\Cloud\PubSub\PubSubClient;

class PubSubFactory implements QueueFactoryInterface
{
    private PubSubClient $client;

    /** @psalm-var array<string, PubSubConsumer>  */
    private array $consumers = [];

    /** @psalm-var array<string, PubSubProducer>  */
    private array $producers = [];

    public function __construct(array $config = [])
    {
        $this->client = new PubSubClient($config);
    }

    public function producer(QueueConfigInterface $config) : PubSubProducer
    {
        if (! $config instanceof PubSubConfig) {
            throw new ESBException('PubSubFactory expects config been instance of PubSubConfig');
        }
        if (! $producer = $this->producers[$config->topic] ?? null) {
            $producer   = new PubSubProducer($this->client->topic($config->topic));

            $this->producers[$config->topic] = $producer;
        }

        return $producer;
    }

    public function consumer(QueueConfigInterface $config) : PubSubConsumer
    {
        if (! $config instanceof PubSubConfig) {
            throw new ESBException('PubSubFactory expects config been instance of PubSubConfig');
        }
        if (! $consumer = $this->consumers[$config->topic] ?? null) {
            $consumer = new PubSubConsumer($this->client->subscribe($config->subscription, $config->topic, $config->options));

            $this->consumers[$config->topic] = $consumer;
        }

        return $consumer;
    }
}
