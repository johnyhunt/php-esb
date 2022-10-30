<?php

declare(strict_types=1);

namespace ESB\DTO;

class TargetResponse
{
    public function __construct(
        public readonly mixed $content,
        public readonly int $statusCode,
        public readonly array $headers = []
    ) {}
}
