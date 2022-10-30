<?php

namespace ESB\Entity\VO;

use ESB\Entity\SyncTable;

class SyncSettings
{
    public function __construct(
        private SyncTable $table,
        private bool $syncOnExist,
        private bool $syncOnChange,
    ) {
    }

    public function table(): SyncTable
    {
        return $this->table;
    }

    public function syncOnChange(): bool
    {
        return $this->syncOnChange;
    }

    public function syncOnExist(): bool
    {
        return $this->syncOnExist;
    }
}
