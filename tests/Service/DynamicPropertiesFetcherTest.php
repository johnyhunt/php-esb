<?php

declare(strict_types=1);

namespace ESB\Test\Service;

use ESB\DTO\IncomeData;
use ESB\Exception\SetupException;
use ESB\Service\DynamicPropertiesFetcher;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class DynamicPropertiesFetcherTest extends TestCase
{
    private readonly DynamicPropertiesFetcher $parser;

    public function setUp(): void
    {
        $twig = new Environment(new ArrayLoader(), [
            'strict_variables' => false,
            'autoescape' => false,
        ]);
        $this->parser = new DynamicPropertiesFetcher($twig);

        parent::setUp();
    }

    /** positive scenario */
    public function testInvokeCase1() : void
    {
        $properties = [
            'key1' => '{{ body.value1 }}',
            'key2' => '{{ body.value2|replace({\'this\': \'fruit\'}) }}',
            'key3' => 'not a template',
            'key4' => 555,
            'key5' => '{{ headers.value1 }}',
        ];
        $data       = new IncomeData(['value1' => '333'], [], ['value1' => 'a44444444', 'value2' => 'this taste'], '');
        $er         = [
            'key1' => 'a44444444',
            'key2' => 'fruit taste',
            'key3' => 'not a template',
            'key4' => 555,
            'key5' => '333',
        ];

        $this->assertEquals($er, ($this->parser)($data, $properties));
    }

    /** wrong template or inappropriate call */
    public function testInvokeCase2() : void
    {
        $properties = [
            'key1' => '{{ unknownCall(body.value1) }}',
        ];
        $data       = new IncomeData(['value1' => '333'], [], ['value1' => 'a44444444', 'value2' => 'this taste'], '');

        $this->expectException(SetupException::class);

        ($this->parser)($data, $properties);
    }

    /** try render unknown key of data */
    public function testInvokeCase3() : void
    {
        $properties = [
            'key1' => '{{ data.value1 }}',
        ];
        $data       = new IncomeData(['value1' => '333'], [], ['value1' => 'a44444444', 'value2' => 'this taste'], '');

        $this->assertEquals(['key1' => ''], ($this->parser)($data, $properties));
    }
}
