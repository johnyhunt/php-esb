<?php

declare(strict_types=1);

namespace ESB\Auth;

use ESB\DTO\TargetRequest;
use ESB\Entity\IntegrationSystem;

interface AuthServiceInterface
{
    public function authenticate(TargetRequest $targetRequest, IntegrationSystem $integrationSystem, array $settings) : void;
}
