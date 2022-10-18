<?php

declare(strict_types=1);

namespace ESB\Middleware\Handler;

use ESB\Entity\Route;

interface ESBHandlerMiddlewareInterface
{
    public function process(array $incomeData, Route $route, ESBDataHandlerInterface $handler);
}