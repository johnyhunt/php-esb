<?php

namespace Example;

use ESB\Entity\SyncRecord;
use ESB\Entity\SyncTable;
use ESB\Repository\SyncRecordRepositoryInterface;

class SyncRecordRepository implements SyncRecordRepositoryInterface
{
    private $tables = [];

    public function findByPk(SyncTable $table, string $fromId): ?SyncRecord
    {
        // Find in any datasource using table name + id as pk
        return null;
    }

    public function store(SyncTable $table, SyncRecord $record): void
    {
        // Store to table
    }
}
