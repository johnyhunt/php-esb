<?php

declare(strict_types=1);

namespace ESB\Middleware;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\Entity\Route;

interface ESBMiddlewareInterface
{
    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler);
}