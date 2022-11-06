<?php

declare(strict_types=1);

namespace ESB\Queue;

use ESB\Entity\VO\AbstractDSN;

interface QueueConfigInterface
{
    public function buildDsn(string $action) : AbstractDSN;
}
