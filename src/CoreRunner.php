<?php

namespace ESB;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;

/**
 * Default framework runner
 * Can be override
 */
class CoreRunner implements CoreRunnerInterface
{
    public function __construct(private readonly Core $core)
    {
    }

    public function runCore(ProcessingData $data, Route $route) : ProcessingData
    {
        return $this->core->run($data, $route);
    }
}
