<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use JsonSerializable;

use function get_object_vars;

class AuthMap implements JsonSerializable
{
    public function __construct(private readonly string $serviceAlias, private readonly array $settings)
    {
    }

    public function serviceAlias() : string
    {
        return $this->serviceAlias;
    }

    public function settings() : array
    {
        return $this->settings;
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
