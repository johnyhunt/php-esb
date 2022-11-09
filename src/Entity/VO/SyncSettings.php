<?php

namespace ESB\Entity\VO;

use ESB\Entity\SyncTable;

class SyncSettings
{
    public function __construct(
        private SyncTable $table,
        private string $pkPath,
        private string $responsePkPath,
        private bool $syncOnExist,
        private bool $syncOnChange,
        private ?string $updateRoteId = null,
    ) {
    }

    public function updateRoteId() : ?string
    {
        return $this->updateRoteId;
    }

    public function pkPath() : string
    {
        return $this->pkPath;
    }

    public function responsePkPath() : string
    {
        return $this->responsePkPath;
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
