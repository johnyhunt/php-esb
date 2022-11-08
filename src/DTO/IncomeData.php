<?php

declare(strict_types=1);

namespace ESB\DTO;

use JsonSerializable;
use function get_object_vars;

class IncomeData implements JsonSerializable
{
    public function __construct(public readonly array $headers, public readonly array $params, public readonly array $body)
    {
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
