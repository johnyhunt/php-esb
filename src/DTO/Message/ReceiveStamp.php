<?php

declare(strict_types=1);

namespace ESB\DTO\Message;

use ESB\Entity\VO\AbstractDSN;

class ReceiveStamp implements StampInterface
{
    public function __construct(public readonly AbstractDSN $routingDsn, public readonly object $nativeMessage)
    {
    }
}
