<?php

declare(strict_types=1);

namespace ESB\Entity;

use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\InputDataMap;

class Route
{
    public function __construct(
        private string            $id,
        private string            $name,
        private IntegrationSystem $fromSystem,
        private AbstractDSN       $fromSystemDsn,
        private InputDataMap      $fromSystemData, // TODO: Must be VO
        private IntegrationSystem $toSystem,
        private AbstractDSN       $toSystemDsn,
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
}
