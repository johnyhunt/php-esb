<?php

declare(strict_types=1);

namespace Example;

use ESB\RouteProviderInterface;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function  __invoke() : array
    {
        return [
            RouteProviderInterface::class  => fn(ContainerInterface $container) => $container->get(RouteProvider::class),
        ];
    }
}
