<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class AuthMap
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
}
