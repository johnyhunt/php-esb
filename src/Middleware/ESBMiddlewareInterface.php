<?php

declare(strict_types=1);

namespace ESB\Middleware;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;

interface ESBMiddlewareInterface
{
    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData;
}
