<?php

declare(strict_types=1);

namespace TestESB;

use ESB\Service\ServerAppSetup;
use Opsway\ESB\Service\ServerAppSetupInterface;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function  __invoke() : array
    {
        return [
            ServerAppSetupInterface::class => fn(ContainerInterface $container) => $container->get(ServerAppSetup::class),
        ];
    }
}
