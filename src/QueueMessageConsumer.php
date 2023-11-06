<?php

declare(strict_types=1);

namespace ESB;

use ESB\Enum\MessageResultEnum;
use ESB\Handlers\QueueMessageHandler;
use ESB\Queue\QueueConfigInterface;
use ESB\Queue\QueueFactoryInterface;

use function gc_collect_cycles;
use function pcntl_async_signals;
use function pcntl_signal;

class QueueMessageConsumer
{
    protected bool $run = true;

    public function __construct(protected readonly QueueFactoryInterface $factory, protected QueueMessageHandler $handler)
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, fn () => $this->run = false);
        pcntl_signal(SIGINT, fn () => $this->run = false);
    }

    public function run(QueueConfigInterface $config) : void
    {
        $consumer = $this->factory->consumer($config);
        while ($this->run === true) {
            if(! $envelope = $consumer->receive($config)) {
                continue;
            }

            $handlerResult = $this->handler->handle($envelope);
            switch ($handlerResult->result) {
                case MessageResultEnum::ACK:
                    $consumer->acknowledge($envelope);
                    break;
                case MessageResultEnum::REQUEUE:
                    $consumer->requeue($envelope, $handlerResult->options->requeueDelay);
                    break;
                default:
                    $consumer->reject($envelope);
            }
            gc_collect_cycles();
        }
    }
}
