<?php

namespace ESB\Middleware;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\DTO\TargetResponse;
use ESB\Entity\Route;

class TransportMiddleware implements ESBMiddlewareInterface
{
    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        return $handler->handle($data->withTargetResponse(new TargetResponse('ok', 200)), $route);
    }
}
