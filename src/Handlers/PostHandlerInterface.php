<?php

namespace ESB\Handlers;

use ESB\DTO\RouteData;

interface PostHandlerInterface
{
    public function handle(RouteData $routeData);
}
