<?php

declare(strict_types=1);

namespace ESB\Entity;

use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\TargetRequestMap;
use ESB\Entity\VO\PostHandler;
use ESB\Entity\VO\SyncSettings;
use JsonSerializable;

use function get_object_vars;

class Route implements JsonSerializable
{
    public function __construct(
        private string            $name,
        private IntegrationSystem $fromSystem,
        private AbstractDSN       $fromSystemDsn,
        private InputDataMap      $fromSystemData,
        private IntegrationSystem $toSystem,
        private AbstractDSN       $toSystemDsn,
        private TargetRequestMap  $toSystemData,
        private ?SyncSettings     $syncSettings,
        private ?array            $postSuccessHandlers = [],
        private ?string           $customRunner = null,
        private ?string           $description = null,
    ) {
    }

    public function fromSystem() : IntegrationSystem
    {
        return $this->fromSystem;
    }

    public function toSystem() : IntegrationSystem
    {
        return $this->toSystem;
    }

    public function description() : ?string
    {
        return $this->description;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function fromSystemDsn() : AbstractDSN
    {
        return $this->fromSystemDsn;
    }

    public function fromSystemData() : InputDataMap
    {
        return $this->fromSystemData;
    }

    public function toSystemData() : TargetRequestMap
    {
        return $this->toSystemData;
    }

    public function syncSettings() : ?SyncSettings
    {
        return $this->syncSettings;
    }

    /** @psalm-return array<int, PostHandler> */
    public function postSuccessHandlers() : array
    {
        return $this->postSuccessHandlers ?? [];
    }

    public function customRunner() : ?string
    {
        return $this->customRunner;
    }

    public function toSystemDsn() : AbstractDSN
    {
        return $this->toSystemDsn;
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
