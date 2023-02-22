<?php

declare(strict_types=1);

namespace ESB\Test\Entity;

use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Entity\VO\EmptyDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\TargetRequestMap;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testGetters() : void
    {
        $route = new Route(
            name: 'some_route',
            fromSystem: new IntegrationSystem(''),
            fromSystemDsn: new EmptyDSN(),
            fromSystemData: $this->createMock(InputDataMap::class),
            toSystem: new IntegrationSystem(''),
            toSystemDsn: new EmptyDSN(),
            toSystemData: $this->createMock(TargetRequestMap::class),
        );
        $this->assertSame('some_route', $route->name());
        $this->assertInstanceOf(IntegrationSystem::class, $route->fromSystem());
        $this->assertInstanceOf(IntegrationSystem::class, $route->toSystem());
        $this->assertNull($route->description());
        $this->assertInstanceOf(EmptyDSN::class, $route->fromSystemDsn());
        $this->assertInstanceOf(EmptyDSN::class, $route->toSystemDsn());
        $this->assertInstanceOf(InputDataMap::class, $route->fromSystemData());
        $this->assertInstanceOf(TargetRequestMap::class, $route->toSystemData());
        $this->assertNull($route->syncSettings());
        $this->assertSame([], $route->postSuccessHandlers());
        $this->assertSame([], $route->postErrorHandlers());
    }
}