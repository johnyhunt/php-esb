<?php

declare(strict_types=1);

namespace ESB\DTO;

class QueueHandlerOptions
{
    public function __construct(public readonly int $requeueDelay = 0, public readonly string $errorMessage = '')
    {
    }
}
