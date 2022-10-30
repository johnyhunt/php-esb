<?php

namespace ESB\Repository;

use ESB\Entity\SyncRecordInterface;

interface SyncRecordRepositoryInterface
{
    public function findByPk(string $pk) : ?SyncRecordInterface;

    public function store(SyncRecordInterface $record) : void;
}
