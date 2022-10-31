<?php

namespace ESB\Repository;

use ESB\Entity\SyncRecord;
use ESB\Entity\SyncTable;

interface SyncRecordRepositoryInterface
{
    public function findByPk(SyncTable $table, string $fromId) : ?SyncRecord;

    public function store(SyncTable $table, SyncRecord $record) : void;
}
