<?php

namespace ESB;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;

interface CoreRunnerInterface
{
    public function runCore(ProcessingData $data, Route $route) : ProcessingData;
}
