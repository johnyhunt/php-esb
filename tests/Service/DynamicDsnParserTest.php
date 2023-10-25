<?php

declare(strict_types=1);

namespace ESB\Test\Service;

use ESB\DTO\IncomeData;
use ESB\Entity\VO\HttpDSN;
use ESB\Service\DynamicDsnParser;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class DynamicDsnParserTest extends TestCase
{
    private DynamicDsnParser $parser;

    public function setUp(): void
    {
        $twig = new Environment(new ArrayLoader(), [
            'strict_variables' => false,
            'autoescape' => false,
        ]);
        $this->parser = new DynamicDsnParser($twig);

        parent::setUp();
    }

    public function testInvokeCase1() : void
    {
        $dsn        = new HttpDSN('POST', '/v1/{{body.id}}/{{params.number}}');
        $incomeData = new IncomeData([], ['number' => 123], ['id' => '456'], '');
        $parsedDsn  = ($this->parser)($incomeData, $dsn);
        $this->assertInstanceOf($dsn::class, $parsedDsn);
        $this->assertEquals('HTTP;POST;/v1/456/123', $parsedDsn->dsn());
    }

    public function testInvokeCase2() : void
    {
        $dsn        = new HttpDSN('POST', '/v1/{{body.id}}/{{params.number}}');
        $incomeData = new IncomeData([], [], [], '');
        $parsedDsn  = ($this->parser)($incomeData, $dsn);

        $this->assertEquals('HTTP;POST;/v1//', $parsedDsn->dsn());
    }
}
