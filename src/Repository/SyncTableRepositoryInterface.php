<?php

declare(strict_types=1);

namespace ESB\Repository;

use ESB\Entity\SyncTable;

interface SyncTableRepositoryInterface
{
    public function store(SyncTable $syncTable) : void;
}
