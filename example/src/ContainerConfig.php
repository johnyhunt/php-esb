<?php

declare(strict_types=1);

namespace Example;

use ESB\Repository\RouteRepositoryInterface;
use Example\Service\DsnInterpreter;
use Example\Service\DsnInterpreterInterface;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function  __invoke() : array
    {
        return [
            RouteRepositoryInterface::class  => fn(ContainerInterface $container) => $container->get(RouteRepository::class),
            DsnInterpreterInterface::class => new DsnInterpreter(),
        ];
    }
}
