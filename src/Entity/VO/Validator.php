<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class Validator
{
    public function __construct(public readonly string $assert, public readonly array $params = [])
    {
    }
}
