<?php

declare(strict_types=1);

namespace ESB\Test\Response;

use ESB\Response\ESBJsonResponse;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ESBJsonResponseTest extends TestCase
{
    public function testWithStatusCase1() : void
    {
        $response = new ESBJsonResponse([]);
        $response = $response->withStatus(400);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Bad Request', $response->getReasonPhrase());
    }

    public function testWithStatusCase2() : void
    {
        $response = new ESBJsonResponse([]);
        $response = $response->withStatus(400, 'Custom reason');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Custom reason', $response->getReasonPhrase());
    }

    public function testWithStatusCase3() : void
    {
        $response = new ESBJsonResponse([]);
        $response = $response->withStatus(430);

        $this->assertSame(430, $response->getStatusCode());
        $this->assertSame('Unknown', $response->getReasonPhrase());
    }

    public function testWithStatusCase4() : void
    {
        $response = new ESBJsonResponse([]);
        $this->expectException(InvalidArgumentException::class);
        $response->withStatus(99);
    }
}
