<?php

declare(strict_types=1);

namespace ESB\Repository;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;

interface CommunicationLogInterface
{
    public function log(Route $route, ProcessingData $processingData) : void;
}
