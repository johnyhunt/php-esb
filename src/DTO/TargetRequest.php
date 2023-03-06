<?php

declare(strict_types=1);

namespace ESB\DTO;

use JsonSerializable;

use function get_object_vars;

class TargetRequest implements JsonSerializable
{
    public function __construct(public string $body, public array $headers = [], public array $params = [])
    {
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
