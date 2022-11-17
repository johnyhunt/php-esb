<?php

declare(strict_types=1);

namespace ESB\Client;

use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;

interface EsbClientInterface
{
    public function send(AbstractDSN $dsn, TargetRequest $targetRequest, string $responseFormat) : TargetResponse;

    public function dsnMatchClass() : string;
}
