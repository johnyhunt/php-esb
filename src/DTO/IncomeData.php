<?php

declare(strict_types=1);

namespace ESB\DTO;

use JsonSerializable;

class IncomeData implements JsonSerializable
{
    public function __construct(
        public readonly array $headers,
        public readonly array $params,
        public readonly array $body,
        public string $requestId,
    ) {
    }

    public function jsonSerialize() : array
    {
        return [
            'headers' => $this->headers,
            'params'  => $this->params,
            'body'    => $this->body,
        ];
    }
}
