<?php

declare(strict_types=1);

namespace ESB\DTO;

use ESB\Enum\MessageResultEnum;

class QueueHandlerResult
{
    public function __construct(public readonly MessageResultEnum $result, public readonly QueueHandlerOptions $options)
    {
    }
}
