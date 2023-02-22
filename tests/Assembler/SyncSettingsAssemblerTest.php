<?php

declare(strict_types=1);

namespace ESB\Test\Assembler;

use Error;
use ESB\Assembler\SyncSettingsAssembler;
use ESB\Entity\VO\SyncSettings;
use PHPUnit\Framework\TestCase;

class SyncSettingsAssemblerTest extends TestCase
{
    public function testInvokeCase1() : void
    {
        $settings     = [
            'pkPath'         => 'some_pk_path',
            'responsePkPath' => 'some_response_pk_path',
            'syncOnExist'    => true,
            'syncOnChange'   => true,
            'updateRouteId'  => 'newRoute',
        ];
        $syncSettings = (new SyncSettingsAssembler())(
            table: ['some_table'],
            syncSettings: $settings,
        );

        $this->assertInstanceOf(SyncSettings::class, $syncSettings);
        $this->assertSame('some_table', $syncSettings->table()->tableName());
        $this->assertSame('some_pk_path', $syncSettings->pkPath());
        $this->assertSame('some_response_pk_path', $syncSettings->responsePkPath());
        $this->assertSame(true, $syncSettings->syncOnExist());
        $this->assertSame(true, $syncSettings->syncOnChange());
        $this->assertSame('newRoute', $syncSettings->updateRouteId());
    }

    public function testInvokeCase2() : void
    {
        $settings     = [
            'pkPath' => 'some_pk_path',
            'responsePkPath' => 'some_response_pk_path',
            'syncOnExist' => true,
            'syncOnChange' => true,
            'updateRouteId' => 'newRoute',
        ];
        $syncSettings = (new SyncSettingsAssembler())(
            table: null,
            syncSettings: $settings,
        );

        $this->assertNull($syncSettings);
    }

    public function testInvokeCase3() : void
    {
        $syncSettings = (new SyncSettingsAssembler())(
            table: ['some_table'],
            syncSettings: null,
        );

        $this->assertNull($syncSettings);
    }

    public function testInvokeCase4() : void
    {
        $this->expectException(Error::class);
        (new SyncSettingsAssembler())(
            table: ['some_table'],
            syncSettings: ['randomKey'],
        );
    }
}
