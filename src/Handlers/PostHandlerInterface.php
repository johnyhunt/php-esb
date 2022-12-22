<?php

namespace ESB\Handlers;

use ESB\DTO\ProcessingData;

interface PostHandlerInterface
{
    public function handle(ProcessingData $routeData) : void;
}
