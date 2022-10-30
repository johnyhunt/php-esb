<?php

namespace ESB\Middleware;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\Entity\Route;
use ESB\Exception\ESBException;
use ESB\Handlers\PostHandlerInterface;

class PostSuccessMiddleware implements ESBMiddlewareInterface
{
    /** @psalm-param array<string, PostHandlerInterface> $customContainerHandlers */
    public function __construct(private readonly array $customContainerHandlers)
    {
    }

    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        // If response from target system not 200, skip this middleware
        if ($data->targetResponse()->statusCode !== 200) {
            return $handler->handle($data, $route);
        }

        foreach ($route->postSuccessHandlers() as $psh) {
            $concreteHandler = $this->customContainerHandlers[$psh->name()] ??
                throw new ESBException("Handler {$psh->name()} isn't registered in container config");
            $concreteHandler->handle($data);
        }

        return $handler->handle($data, $route);
    }
}
