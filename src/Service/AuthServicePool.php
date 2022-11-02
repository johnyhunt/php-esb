<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Auth\AuthServiceInterface;
use ESB\Exception\ESBException;

use function sprintf;

class AuthServicePool
{
    /** @psalm-var array<string, AuthServiceInterface> $pool  */
    private array $pool;

    public function __construct(AuthServiceInterface ...$services)
    {
        foreach ($services as $service) {
            if ($this->pool[$service->matchAlias()] ?? null) {
                throw new ESBException(sprintf('AuthServicePool::invalid setup, alias duplicate %s', $service->matchAlias()));
            }
            $this->pool[$service->matchAlias()] = $service;
        }
    }

    public function get(string $alias) : AuthServiceInterface
    {
        return $this->pool[$alias] ?? throw new ESBException(sprintf('AuthServicePool::get unknown alias %s', $alias));
    }
}
