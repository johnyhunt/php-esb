<?php

declare(strict_types=1);

namespace ESB;

use ESB\DTO\RouteData;
use ESB\Entity\Route;

interface ESBCoreInterface
{
    public function run(RouteData $data, Route $route);
}
