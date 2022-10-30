<?php

declare(strict_types=1);

namespace ESB\Entity;

use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\OutputDataMap;
use ESB\Entity\VO\PostHandler;
use ESB\Entity\VO\SyncTable;

class Route
{
    public function __construct(
        private string            $id,
        private string            $name,
        private IntegrationSystem $fromSystem,
        private AbstractDSN       $fromSystemDsn,
        private InputDataMap      $fromSystemData,
        private IntegrationSystem $toSystem,
        private AbstractDSN       $toSystemDsn,
        private OutputDataMap     $toSystemData,
        private ?SyncTable        $syncTable,
        private ?array            $postSuccessHandlers = [],
        private ?string           $description = null,
    ) {
    }

    public function fromSystemDsn(): AbstractDSN
    {
        return $this->fromSystemDsn;
    }

    public function fromSystemData() : InputDataMap
    {
        return $this->fromSystemData;
    }

    public function syncTable() : ?SyncTable
    {
        return $this->syncTable;
    }

    public function toSystemData() : OutputDataMap
    {
        return $this->toSystemData;
    }

    /** @psalm-return array<int, PostHandler> */
    public function postSuccessHandlers() : array
    {
        return $this->postSuccessHandlers;
    }
}
