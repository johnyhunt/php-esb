<?php

declare(strict_types=1);

namespace ESB\Dto;

class IncomeData
{
    public function __construct(public readonly array $headers, public readonly array $params, public readonly array $body)
    {
    }
}
