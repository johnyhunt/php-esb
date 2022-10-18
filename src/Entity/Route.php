<?php

declare(strict_types=1);

namespace ESB\Entity;

use ESB\Entity\VO\AbstractDSN;

class Route
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $incomeSystem,
        public readonly AbstractDSN $fromSystemDsn,
        public readonly array $incomeData,
        public readonly string $description = '',
    ) {
    }
}
