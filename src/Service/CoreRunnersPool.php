<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\CoreRunner;
use ESB\CoreRunnerInterface;
use ESB\Exception\ESBException;

use function sprintf;

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
        if ($alias === null) {
            return $this->coreRunner;
        }

        return $this->runners[$alias] ?? throw new ESBException(sprintf('CoreRunnersPool - runner for %s isn`t set', $alias));
    }
}
