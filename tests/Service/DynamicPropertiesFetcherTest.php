<?php

declare(strict_types=1);

namespace ESB\Test\Service;

use ESB\DTO\IncomeData;
use ESB\Service\DynamicPropertiesFetcher;
use PHPUnit\Framework\TestCase;

class DynamicPropertiesFetcherTest extends TestCase
{
    public function testInvokeCase1() : void
    {
        $incomeData = new IncomeData(
            headers: ['foo' => 'bar'],
            params: ['authPath' => 'https://example.com/auth'],
            body: ['id' => 123456, 'name' => 'John Doe'],
            requestId: ''
        );
        $params     = [
            'body-id'      => '{{      body.id       }}',
            'foo-header'   => '{{headers.foo}}',
            'boo-header'   => '{{headers.boo}}',
            'static-param' => 'static-value',
            'auth-path'    => '{{ params.authPath }}',
        ];

        $this->assertSame(
            [
                'body-id'      => 123456,
                'foo-header'   => 'bar',
                'boo-header'   => null,
                'static-param' => 'static-value',
                'auth-path'     => 'https://example.com/auth'
            ],
            (new DynamicPropertiesFetcher())($incomeData, $params));
    }
}
