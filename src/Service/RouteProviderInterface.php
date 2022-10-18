<?php

declare(strict_types=1);

namespace Opsway\ESB\Service;

use Opsway\ESB\Entity\Route;
use Opsway\ESB\Exception\ESBException;

interface RouteProviderInterface
{
    /** @throws ESBException */
    public function get(string $key) : Route;

    /** @psalm-return array<string, Route> */
    public function loadAll() : array;
}
