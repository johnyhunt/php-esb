<?php

namespace ESB;

use ESB\DTO\RouteData;
use ESB\Entity\Route;

interface CoreRunnerInterface
{
    public function runCore(RouteData $data, Route $route) : void;
}
