<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\Entity\Route;

interface ESBCoreHandlerInterface
{
    public function run(array $incomeData, Route $route);
}
