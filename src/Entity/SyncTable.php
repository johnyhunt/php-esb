<?php

declare(strict_types=1);

namespace ESB\Entity;

class SyncTable
{
    public function __construct(
        private string $tableName,
    ) {
    }

    public function tableName(): string
    {
        return $this->tableName;
    }
}
