<?php

declare(strict_types=1);

namespace ESB\Repository;

use ESB\DTO\RouteData;
use ESB\Entity\VO\SyncTable;

interface SyncTableRepositoryInterface
{
    public function wasSynced(SyncTable $syncTable) : bool;

    public function store(RouteData $data, SyncTable $syncTable) : void;
}
