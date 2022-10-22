<?php

declare(strict_types=1);

namespace ESB\Entity;

use ESB\Entity\VO\AbstractDSN;

class Route
{
    public function __construct(
        private string      $id,
        private string      $name,
        private string      $fromSystem,
        private AbstractDSN $fromSystemDsn,
        private array       $incomeData,
        private ?string     $description = null,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function fromSystem(): string
    {
        return $this->fromSystem;
    }

    public function fromSystemDsn(): AbstractDSN
    {
        return $this->fromSystemDsn;
    }

    public function incomeData(): array
    {
        return $this->incomeData;
    }

    public function description(): string
    {
        return $this->description;
    }
}
