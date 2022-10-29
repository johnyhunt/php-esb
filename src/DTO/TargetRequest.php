<?php

declare(strict_types=1);

namespace ESB\DTO;

class TargetRequest
{
    public function __construct(public string $body, public array $headers = [])
    {
    }
}
