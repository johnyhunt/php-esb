<?php

namespace ESB\Entity\VO;

class PostHandler
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
