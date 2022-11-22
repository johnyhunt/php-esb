<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Service\PostSuccessHandlersPool;

class PostSuccessMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly PostSuccessHandlersPool $customContainerHandlers)
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        // This middleware only for success requests and processes
        if (! $data->targetResponse()->isSuccess) {
            return $handler->handle($data, $route);
        }

        foreach ($route->postSuccessHandlers() as $psh) {
            $concreteHandler = $this->customContainerHandlers->get($psh->name());
            $concreteHandler->handle($data);
        }

        return $handler->handle($data, $route);
    }
}
