<?php

namespace ESB\Entity\VO;

use ESB\Entity\SyncTable;
use JsonSerializable;

class SyncSettings implements JsonSerializable
{
    public function __construct(
        private SyncTable $table,
        private string    $pkPath,
        private string    $responsePkPath,
        private bool      $syncOnExist,
        private bool      $syncOnChange,
        private ?string   $updateRouteId = null,
    ) {
    }

    public function updateRouteId() : ?string
    {
        return $this->updateRouteId;
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

    public function jsonSerialize() : array
    {
        return [
            'pkPath'         => $this->pkPath,
            'responsePkPath' => $this->responsePkPath,
            'syncOnExist'    => $this->syncOnExist,
            'syncOnChange'   => $this->syncOnChange,
            'updateRouteId'   => $this->updateRouteId,
        ];
    }
}
