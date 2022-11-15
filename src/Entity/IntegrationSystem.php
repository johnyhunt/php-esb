<?php

namespace ESB\Entity;

class IntegrationSystem
{
    public function __construct(private string $code)
    {
    }

    public function code() : string
    {
        return $this->code;
    }
}
