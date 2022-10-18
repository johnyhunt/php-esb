<?php

declare(strict_types=1);

namespace Opsway\ESB;

use Opsway\ESB\Service\RouteProvider;
use Opsway\ESB\Service\RouteProviderInterface;

class ContainerConfig
{
    public function __invoke() : array
    {
        return [
            RouteProviderInterface::class => new RouteProvider(),
        ];
    }
}
