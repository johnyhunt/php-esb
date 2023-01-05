<?php

declare(strict_types=1);

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Exception\NonSuccessException;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Service\PostErrorHandlersPool;
use Throwable;

class PostErrorMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly PostErrorHandlersPool $customContainerHandlers)
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        // This middleware only for error requests and processes
        if ($data->targetResponse()->isSuccess) {
            return $handler->handle($data, $route);
        }

        try {
            foreach ($route->postErrorHandlers() as $psh) {
                $concreteHandler = $this->customContainerHandlers->get($psh->name());
                $concreteHandler->handle($data);
            }
        } catch (Throwable $e) {
            throw new NonSuccessException($route, $data, $e);
        }

        throw new NonSuccessException($route, $data);
    }
}
