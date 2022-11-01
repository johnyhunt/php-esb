<?php

namespace Example\Runner;

use ESB\Core;
use ESB\CoreRunnerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;

class MyCustomRunner implements CoreRunnerInterface
{
    public function __construct(private readonly Core $core)
    {
    }

    public function runCore(ProcessingData $data, Route $route) : ProcessingData
    {
        return $this->core->run($data, $route);
    }
}
