<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\CoreRunner;
use ESB\CoreRunnerInterface;

class CoreRunnersPool
{
    private array $runners = [];

    public function __construct(private readonly CoreRunner $coreRunner)
    {
    }

    public function add(string $alias, CoreRunnerInterface $runner) : void
    {
        $this->runners[$alias] = $runner;
    }

    public function get(?string $alias) : CoreRunnerInterface
    {
        if (! $alias) {
            return $this->coreRunner;
        }

        return $this->runners[$alias] ?? $this->coreRunner;
    }
}
