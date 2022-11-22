<?php

namespace ESB\Entity\VO;

use JsonSerializable;

class PostHandler implements JsonSerializable
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function jsonSerialize() : string
    {
        return $this->name;
    }
}
