<?php

namespace Example\Runner;

use ESB\Core;
use ESB\CoreRunnerInterface;
use ESB\DTO\RouteData;
use ESB\Entity\Route;

class MyCustomRunner implements CoreRunnerInterface
{
    public function __construct(private readonly Core $core)
    {
    }

    public function runCore(RouteData $data, Route $route): void
    {
        $this->core->run($data, $route);
    }
}
