<?php

namespace Example\Handlers\Success;

use ESB\DTO\RouteData;
use ESB\Handlers\PostHandlerInterface;

class MyPostSuccessHandler implements PostHandlerInterface
{
    public function handle(RouteData $routeData)
    {
        echo "I'M in my post handler \n\r";
    }
}
