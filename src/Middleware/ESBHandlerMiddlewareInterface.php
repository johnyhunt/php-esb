<?php

declare(strict_types=1);

namespace ESB\Middleware;

use ESB\Dto\RouteData;
use ESB\Entity\Route;

interface ESBHandlerMiddlewareInterface
{
    public function process(RouteData $data, Route $route, ESBDataHandlerInterface $handler);
}