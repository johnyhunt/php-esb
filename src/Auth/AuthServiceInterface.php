<?php

declare(strict_types=1);

namespace ESB\Auth;

use ESB\DTO\TargetRequest;

interface AuthServiceInterface
{
    public function authenticate(TargetRequest $targetRequest, array $settings) : void;
}
