<?php

declare(strict_types=1);

namespace ESB\Repository;

use ESB\Entity\Route;
use ESB\Exception\ESBException;

interface RouteRepositoryInterface
{
    /** @throws ESBException */
    public function get(string $fromSystemDsn) : Route;

    /** @psalm-return array<string, Route> */
    public function loadAll() : array;

    public function store(Route $route) : void;

    public function delete(Route $route) : void;
}
