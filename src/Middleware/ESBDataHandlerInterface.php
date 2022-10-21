<?php

declare(strict_types=1);

namespace ESB\Middleware;

use ESB\Dto\RouteData;
use ESB\Entity\Route;

interface ESBDataHandlerInterface
{
    public function handle(RouteData $data, Route $route);
}
