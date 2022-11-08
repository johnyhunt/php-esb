<?php

declare(strict_types=1);

namespace ESB;

use ESB\Enum\MessageResultEnum;
use ESB\Handlers\QueueMessageHandler;
use ESB\Queue\QueueConfigInterface;
use ESB\Queue\QueueFactoryInterface;

use function pcntl_async_signals;
use function pcntl_signal;

class QueueConsumer
{
    private bool $run = true;

    public function __construct(private readonly QueueFactoryInterface $factory, private QueueMessageHandler $handler)
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, fn () => $this->run = false);
        pcntl_signal(SIGINT, fn () => $this->run = false);
    }

    public function run(QueueConfigInterface $config) : void
    {
        $consumer = $this->factory->consumer($config);
        while ($this->run === true) {
            if(! $message = $consumer->receive()) {
                continue;
            }

            $handlerResult = $this->handler->handle($message);
            switch ($handlerResult->result) {
                case MessageResultEnum::ACK:
                    $consumer->acknowledge($message);
                    break;
                case MessageResultEnum::REQUEUE:
                    $consumer->requeue($message, $handlerResult->options->requeueDelay);
                    break;
                default:
                    $consumer->reject($message);
            }
        }
    }
}
