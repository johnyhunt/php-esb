<?php

namespace Example\Handlers\Success;

use ESB\DTO\ProcessingData;
use ESB\Handlers\PostHandlerInterface;

class MyPostSuccessHandler implements PostHandlerInterface
{
    public function handle(ProcessingData $routeData) : void
    {
        echo "I'M in my post handler \n\r";
    }
}
