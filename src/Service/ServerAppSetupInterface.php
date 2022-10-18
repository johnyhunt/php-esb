<?php

declare(strict_types=1);

namespace ESB\Service;

use Slim\App;

interface ServerAppSetupInterface
{
    public function __invoke(App $app) : void;
}
