<?php

declare(strict_types=1);

namespace ESB\DTO\Message;

class Message
{
    public function __construct(public readonly string $body, public readonly string $action, public readonly array $attributes)
    {
    }
}
