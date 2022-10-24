<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class InputDataMap
{
    public function __construct(public readonly ValidationRule $data, public array $headers = [], public array $properties = [])
    {
    }
}
