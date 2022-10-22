<?php

declare(strict_types=1);

namespace ESB\Middleware;

use ESB\DTO\RouteData;
use ESB\Entity\Route;

class ValidatorMiddleware implements ESBMiddlewareInterface
{
    public function process(RouteData $data, Route $route, ESBDataHandlerInterface $handler)
    {
        var_dump('ValidatorMiddleware::process');
        // TODO: Implement validation.
        return $handler->handle($data, $route);
    }
}
