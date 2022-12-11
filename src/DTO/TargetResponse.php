<?php

declare(strict_types=1);

namespace ESB\DTO;

class TargetResponse
{
    public function __construct(
        public readonly array $content,
        public readonly int $responseTime,
        public readonly bool $isSuccess = true,
        public readonly int $statusCode = 200,
        public readonly array $headers = []
    ) {}
}
