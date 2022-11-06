<?php

declare(strict_types=1);

namespace ESB\Queue;

interface QueueFactoryInterface
{
    public function producer(QueueConfigInterface $config) : QueueProducerInterface;
    public function consumer(QueueConfigInterface $config) : QueueConsumerInterface;
}
