<?php

declare(strict_types=1);

namespace ESB\Middleware\Handler;

use ESB\Entity\Route;

class ValidatorMiddleware implements ESBHandlerMiddlewareInterface
{
    public function process(array $incomeData, Route $route, ESBDataHandlerInterface $handler)
    {
        var_dump('ValidatorMiddleware::process');
        // TODO: Implement validation.
        return $handler->handle($incomeData, $route);
    }
}
