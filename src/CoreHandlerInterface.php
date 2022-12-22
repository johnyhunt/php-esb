<?php

declare(strict_types=1);

namespace ESB;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;

interface CoreHandlerInterface
{
    public function handle(ProcessingData $data, Route $route) : ProcessingData;
}
