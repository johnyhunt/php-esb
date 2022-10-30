<?php

declare(strict_types=1);

namespace ESB\Entity;

use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\OutputDataMap;
use ESB\Entity\VO\PostHandler;
use ESB\Entity\VO\SyncSettings;

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
        private ?SyncSettings     $syncSettings,
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

    public function toSystemData() : OutputDataMap
    {
        return $this->toSystemData;
    }

    public function syncSettings(): ?SyncSettings
    {
        return $this->syncSettings;
    }

    /** @psalm-return array<int, PostHandler> */
    public function postSuccessHandlers() : array
    {
        return $this->postSuccessHandlers;
    }
}
