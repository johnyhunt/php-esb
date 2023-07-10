<?php

namespace ESB\Entity;

use JsonSerializable;

use function get_object_vars;

class IntegrationSystem implements JsonSerializable
{
    public function __construct(private string $code, array $config = [])
    {
    }

    public function code() : string
    {
        return $this->code;
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
