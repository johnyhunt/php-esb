<?php

declare(strict_types=1);

namespace ESB\Test\Assembler;

use ESB\Assembler\DsnInterpreterInterface;
use ESB\Assembler\InputDataMapAssembler;
use ESB\Assembler\RouteEntityAssembler;
use ESB\Assembler\SyncSettingsAssembler;
use ESB\Assembler\ToSystemDataAssembler;
use ESB\Entity\Route;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\EmptyDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\SyncSettings;
use ESB\Entity\VO\TargetRequestMap;
use PHPUnit\Framework\TestCase;

class RouteEntityAssemblerTest extends TestCase
{
    private readonly InputDataMapAssembler $inputDataMapAssembler;
    private readonly DsnInterpreterInterface $dsnInterpreter;
    private readonly SyncSettingsAssembler $syncSettingsAssembler;
    private readonly ToSystemDataAssembler $toSystemDataAssembler;
    private readonly RouteEntityAssembler $routeEntityAssembler;
    public function setUp(): void
    {
       $this->inputDataMapAssembler = $this->createMock(InputDataMapAssembler::class);
       $this->dsnInterpreter        = $this->createMock(DsnInterpreterInterface::class);
       $this->syncSettingsAssembler = $this->createMock(SyncSettingsAssembler::class);
       $this->toSystemDataAssembler = $this->createMock(ToSystemDataAssembler::class);
       $this->routeEntityAssembler  = (new RouteEntityAssembler(
            inputDataMapAssembler: $this->inputDataMapAssembler,
            dsnInterpreter: $this->dsnInterpreter,
            syncSettingsAssembler: $this->syncSettingsAssembler,
            toSystemDataAssembler: $this->toSystemDataAssembler,
        ));
        parent::setUp();
    }

    public function testInvoke() : void
    {
        $this->dsnInterpreter->expects($this->exactly(2))->method('__invoke')->willReturn(new EmptyDSN());
        $this->inputDataMapAssembler->expects($this->once())->method('__invoke')->willReturn($this->createMock(InputDataMap::class));
        $this->syncSettingsAssembler->expects($this->once())->method('__invoke')->willReturn($this->createMock(SyncSettings::class));
        $this->toSystemDataAssembler->expects($this->once())->method('__invoke')->willReturn($this->createMock(TargetRequestMap::class));

        $routeEntity = ($this->routeEntityAssembler)(
            [
                [
                    'name'                => 'some_route_id',
                    'description'         => '',
                    'fromSystem'          => ['code' => 'some_from_system_code'],
                    'toSystem'            => ['code' => 'some_to_system_code'],
                    'fromSystemDsn'       => '',
                    'toSystemDsn'         => '',
                    'fromSystemData'      => [],
                    'toSystemData'        => [],
                    'syncTable'           => null,
                    'syncSettings'        => null,
                    'postSuccessHandlers' => [],
                    'postErrorHandlers'   => [],
                    'customRunner'        => null,
                ]
            ]
        )[0];

        $this->assertInstanceOf(Route::class, $routeEntity);
        $this->assertSame('some_route_id', $routeEntity->name());
        $this->assertInstanceOf(InputDataMap::class, $routeEntity->fromSystemData());
        $this->assertInstanceOf(AbstractDSN::class, $routeEntity->fromSystemDsn());
        $this->assertInstanceOf(SyncSettings::class, $routeEntity->syncSettings());
        $this->assertInstanceOf(TargetRequestMap::class, $routeEntity->toSystemData());
        $this->assertInstanceOf(AbstractDSN::class, $routeEntity->toSystemDsn());
        $this->assertNull($routeEntity->customRunner());
        $this->assertSame([], $routeEntity->postErrorHandlers());
        $this->assertSame([], $routeEntity->postSuccessHandlers());
    }

    public function testBuildRoute() : void
    {
        $this->dsnInterpreter->expects($this->exactly(2))->method('__invoke')->willReturn(new EmptyDSN());
        $this->inputDataMapAssembler->expects($this->once())->method('__invoke')->willReturn($this->createMock(InputDataMap::class));
        $this->syncSettingsAssembler->expects($this->once())->method('__invoke')->willReturn($this->createMock(SyncSettings::class));
        $this->toSystemDataAssembler->expects($this->once())->method('__invoke')->willReturn($this->createMock(TargetRequestMap::class));

        $routeEntity = $this->routeEntityAssembler->buildRoute(
            [
                'name'                => 'some_route_id',
                'description'         => '',
                'fromSystem'          => ['code' => 'some_from_system_code'],
                'toSystem'            => ['code' => 'some_to_system_code'],
                'fromSystemDsn'       => '',
                'toSystemDsn'         => '',
                'fromSystemData'      => [],
                'toSystemData'        => [],
                'syncTable'           => ['some_table'],
                'syncSettings'        => [],
                'postSuccessHandlers' => [],
                'postErrorHandlers'   => [],
                'customRunner'        => null,
            ]
        );

        $this->assertInstanceOf(Route::class, $routeEntity);
        $this->assertSame('some_route_id', $routeEntity->name());
        $this->assertInstanceOf(InputDataMap::class, $routeEntity->fromSystemData());
        $this->assertInstanceOf(AbstractDSN::class, $routeEntity->fromSystemDsn());
        $this->assertInstanceOf(SyncSettings::class, $routeEntity->syncSettings());
        $this->assertInstanceOf(TargetRequestMap::class, $routeEntity->toSystemData());
        $this->assertInstanceOf(AbstractDSN::class, $routeEntity->toSystemDsn());
        $this->assertNull($routeEntity->customRunner());
        $this->assertSame([], $routeEntity->postErrorHandlers());
        $this->assertSame([], $routeEntity->postSuccessHandlers());
    }
}
