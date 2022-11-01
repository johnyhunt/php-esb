<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\DTO\TargetResponse;
use ESB\Entity\Route;
use ESB\Middleware\ESBMiddlewareInterface;

class TransportMiddleware implements ESBMiddlewareInterface
{
    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        // 1. Get DSN and detect transport sending interface
            // 1.1 For sync interface, with use default sync transport
            // 1.2 For async - trigger exception if not setup async transport

        // 2. Authorize request (if you need)

        // 3. Using DSN(Method sending) send body $data->targetRequest()->body to target system (ServerDSN->path or QueueDSN->action)

        // 4. $handler->handle with withTargetResponse

        return $handler->handle($data->withTargetResponse(new TargetResponse('ok', 200)), $route);
    }
}
