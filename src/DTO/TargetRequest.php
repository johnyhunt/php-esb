<?php

declare(strict_types=1);

namespace ESB\DTO;

class TargetRequest
{
    public function __construct(public array $body = [], public array $headers = [])
    {
    }
}
