<?php

declare(strict_types=1);

namespace ESB\Test\Service;

use ESB\DTO\IncomeData;
use ESB\Entity\VO\HttpDSN;
use ESB\Exception\RouteConfigException;
use ESB\Service\DynamicDsnParser;
use PHPUnit\Framework\TestCase;

class DynamicDsnParserTest extends TestCase
{
    public function testInvokeCase1() : void
    {
        $dsn        = new HttpDSN('POST', '/v1/{{body.id}}/{{params.number}}');
        $incomeData = new IncomeData([], ['number' => 123], ['id' => '456'], '');
        $parsedDsn  = (new DynamicDsnParser())($incomeData, $dsn);
        $this->assertInstanceOf($dsn::class, $parsedDsn);
        $this->assertEquals('HTTP;POST;/v1/456/123', $parsedDsn->dsn());
    }

    public function testInvokeCase2() : void
    {
        $dsn        = new HttpDSN('POST', '/v1/{{body.id}}/{{params.number}}');
        $incomeData = new IncomeData([], [], [], '');

        $this->expectException(RouteConfigException::class);

        (new DynamicDsnParser())($incomeData, $dsn);
    }
}
