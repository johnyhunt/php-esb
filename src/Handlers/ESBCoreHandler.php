<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\Entity\Route;
use ESB\Middleware\Handler\ESBDataHandlerInterface;
use ESB\Middleware\Handler\ESBHandlerMiddlewareInterface;
use Psr\Container\ContainerInterface;

class ESBCoreHandler implements ESBCoreHandlerInterface
{
    private ESBDataHandlerInterface $handler;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->handler = new class () implements ESBDataHandlerInterface {
            public function handle(array $incomeData, Route $route)
            {
            }
        };
    }

    public function setUpMiddlewares(string ...$classes) : void
    {
        foreach ($classes as $class) {
            if (! $middleware = $this->container->get($class)) {
                continue;
            }
            if (! $middleware instanceof ESBHandlerMiddlewareInterface) {
                continue;
            }
            $next          = $this->handler;
            $this->handler = new class ($middleware, $next) implements ESBDataHandlerInterface {
                public function __construct(private readonly ESBHandlerMiddlewareInterface $middleware, private readonly ESBDataHandlerInterface $next)
                {
                }

                public function handle(array $incomeData, Route $route)
                {
                    return $this->middleware->process($incomeData, $route, $this->next);
                }
            };
        }
    }

    public function run(array $incomeData, Route $route)
    {
        $this->handler->handle($incomeData, $route);
    }
}
