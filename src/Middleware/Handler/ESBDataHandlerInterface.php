<?php

declare(strict_types=1);

namespace ESB\Middleware\Handler;

use ESB\Entity\Route;

interface ESBDataHandlerInterface
{
    public function handle(array $incomeData, Route $route);
}
