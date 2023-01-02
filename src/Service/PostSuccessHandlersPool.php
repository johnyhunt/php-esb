<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Exception\SetupException;
use ESB\Handlers\PostHandlerInterface;

class PostSuccessHandlersPool
{
    /** @psalm-var array<string, PostHandlerInterface>  */
    private array $handlers = [];

    public function add(string $alias, PostHandlerInterface $handler) : void
    {
        $this->handlers[$alias] = $handler;
    }

    public function get(string $alias) : PostHandlerInterface
    {
        return $this->handlers[$alias] ?? throw new SetupException(sprintf('PostSuccessHandlersPool - handler %s isn`t registered in container config', $alias));
    }
}
