<?php

declare(strict_types=1);

namespace ESB;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Middleware\ESBMiddlewareInterface;

class Core
{
    private CoreHandlerInterface $handler;

    public function __construct(ESBMiddlewareInterface ...$middlewares)
    {
        $this->handler = new class () implements CoreHandlerInterface {
            public function handle(ProcessingData $data, Route $route) : ProcessingData
            {
                return $data;
            }
        };

        foreach ($middlewares as $middleware) {
            $next          = $this->handler;
            $this->handler = new class ($middleware, $next) implements CoreHandlerInterface {
                public function __construct(private readonly ESBMiddlewareInterface $middleware, private readonly CoreHandlerInterface $next)
                {
                }

                public function handle(ProcessingData $data, Route $route)
                {
                    return $this->middleware->process($data, $route, $this->next);
                }
            };
        }
    }

    public function run(ProcessingData $data, Route $route) : ProcessingData
    {
        return $this->handler->handle($data, $route);
    }
}
