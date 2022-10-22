<?php

declare(strict_types=1);

namespace ESB;

use ESB\DTO\RouteData;
use ESB\Entity\Route;
use ESB\Exception\ESBException;
use ESB\Middleware\ESBDataHandlerInterface;
use ESB\Middleware\ESBMiddlewareInterface;
use Psr\Container\ContainerInterface;

class Core implements CoreInterface
{
    private ESBDataHandlerInterface $handler;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->handler = new class () implements ESBDataHandlerInterface {
            public function handle(RouteData $data, Route $route)
            {
            }
        };
    }

    public function setUpMiddlewares(string ...$classes) : void
    {
        foreach ($classes as $class) {
            if (! $middleware = $this->container->get($class)) {
                throw new ESBException("Middleware not found in container class");
            }
            if (! $middleware instanceof ESBMiddlewareInterface) {
                throw new ESBException("Middleware has to implement ESBMiddlewareInterface");
            }
            $next          = $this->handler;
            $this->handler = new class ($middleware, $next) implements ESBDataHandlerInterface {
                public function __construct(private readonly ESBMiddlewareInterface $middleware, private readonly ESBDataHandlerInterface $next)
                {
                }

                public function handle(RouteData $data, Route $route)
                {
                    return $this->middleware->process($data, $route, $this->next);
                }
            };
        }
    }

    public function run(RouteData $data, Route $route)
    {
        $this->handler->handle($data, $route);
    }
}
