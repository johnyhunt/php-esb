<?php

declare(strict_types=1);

namespace ESB\Test\Assembler;

use Error;
use ESB\Assembler\ToSystemDataAssembler;
use ESB\Entity\VO\AuthMap;
use ESB\Entity\VO\TargetRequestMap;
use PHPUnit\Framework\TestCase;

class ToSystemDataAssemblerTest extends TestCase
{
    public function testInvokeCase1() : void
    {
        $input = [
             'headers'        => [],
             'template'       => '{{body|json_encode}}',
             'responseFormat' => 'json',
             'auth'           => [
                 'serviceAlias' => 'someAlias',
                 'settings'     => []
             ]
        ];
        $targetRequestMap = (new ToSystemDataAssembler())($input);
        $this->assertInstanceOf(TargetRequestMap::class, $targetRequestMap);
        $this->assertSame([], $targetRequestMap->headers());
        $this->assertSame('{{body|json_encode}}', $targetRequestMap->template());
        $this->assertSame('json', $targetRequestMap->responseFormat());
        $this->assertInstanceOf(AuthMap::class, $targetRequestMap->auth());
        $this->assertSame('someAlias', $targetRequestMap->auth()->serviceAlias());
        $this->assertSame([], $targetRequestMap->auth()->settings());
    }

    public function testInvokeCase2() : void
    {
        $input = [
            'headers'        => [],
            'template'       => null,
            'responseFormat' => 'json',
            'auth'           => null
        ];
        $targetRequestMap = (new ToSystemDataAssembler())($input);
        $this->assertInstanceOf(TargetRequestMap::class, $targetRequestMap);
        $this->assertSame([], $targetRequestMap->headers());
        $this->assertNull($targetRequestMap->template());
        $this->assertSame('json', $targetRequestMap->responseFormat());
        $this->assertNull($targetRequestMap->auth());
    }

    public function testInvokeCase3() : void
    {
        $input = [
            'headers'        => [],
            'template'       => null,
            'responseFormat' => 'json',
            'randomKey'      => 'json',
            'auth'           => null
        ];
        $this->expectException(Error::class);
        (new ToSystemDataAssembler())($input);
    }
}
