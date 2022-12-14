<?php

declare(strict_types=1);

namespace ESB\Repository;

use ESB\Entity\Route;

interface RouteRepositoryInterface
{
    public function get(string $fromSystemDsn) : Route;

    public function getByName(string $name) : Route;

    /** @psalm-return array<string, Route> */
    public function loadAll() : array;

    public function store(Route $route) : void;

    public function delete(Route $route) : void;
}
