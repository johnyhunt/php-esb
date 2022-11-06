<?php

declare(strict_types=1);

namespace ESB\DTO;

class TargetResponse
{
    public function __construct(
        public readonly array $content,
        public readonly int $statusCode,
        public readonly array $headers = []
    ) {}
}
