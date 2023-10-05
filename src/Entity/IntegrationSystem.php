<?php

namespace ESB\Entity;

use JsonSerializable;

use function get_object_vars;

class IntegrationSystem implements JsonSerializable
{
    /** @psalm-param array<string, string> $hosts */
    public function __construct(private string $code, private array $config = [], private array $hosts = [])
    {
    }

    public function code() : string
    {
        return $this->code;
    }

    public function config() : array
    {
        return $this->config;
    }

    /** @psalm-return array<string, string> */
    public function hosts() : array
    {
        return $this->hosts;
    }

    public function setting(string $key) : string|int|bool|float|null
    {
        return $this->config[$key] ?? null;
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
