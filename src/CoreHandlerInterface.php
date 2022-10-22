<?php

declare(strict_types=1);

namespace ESB;

use ESB\DTO\RouteData;
use ESB\Entity\Route;

interface CoreHandlerInterface
{
    public function handle(RouteData $data, Route $route);
}
