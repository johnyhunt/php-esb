<?php

declare(strict_types=1);

namespace ESB;

use ESB\Entity\Route;
use ESB\Exception\ESBException;

interface RouteProviderInterface
{
    /** @throws ESBException */
    public function get(string $key) : Route;

    /** @psalm-return array<string, Route> */
    public function loadAll() : array;
}
