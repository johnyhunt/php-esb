<?php

declare(strict_types=1);

namespace Opsway\ESB\Entity;

use function implode;

class Route
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $fromSystem,
        public readonly string $toSystem,
        public readonly SystemTransport $fromSystemTransport,
        public readonly SystemTransport $toSystemTransport,
        public readonly string $version = 'v1',
        public readonly ?HttpMethod $fromSystemTransportMethod = null,
    ) {
    }

    public function key() : string
    {
        return implode('/', [$this->version, $this->fromSystem, $this->toSystem, $this->name]);
    }
}
