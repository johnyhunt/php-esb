<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Auth\AuthServiceInterface;
use ESB\Exception\ESBException;

use function sprintf;

class AuthServicePool
{
    /** @psalm-var array<string, AuthServiceInterface> $pool  */
    private array $pool = [];

    public function add(string $alias, AuthServiceInterface $service) : void
    {
        $this->pool[$alias] = $service;
    }

    public function get(string $alias) : AuthServiceInterface
    {
        return $this->pool[$alias] ?? throw new ESBException(sprintf('AuthServicePool::get unknown alias %s', $alias));
    }
}
