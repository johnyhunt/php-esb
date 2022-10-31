<?php

namespace ESB;

use ESB\DTO\RouteData;
use ESB\Entity\Route;

/**
 * Default framework runner
 * Can be override
 */
class CoreRunner implements CoreRunnerInterface
{
    public function __construct(private readonly Core $core)
    {
    }

    public function runCore(RouteData $data, Route $route): void
    {
        $this->core->run($data, $route);
    }
}
