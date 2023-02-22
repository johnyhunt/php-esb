<?php

declare(strict_types=1);

namespace ESB\Test\Assembler;

use ESB\Assembler\InputDataMapAssembler;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\ValidationRule;
use PHPUnit\Framework\TestCase;

class InputDataMapAssemblerTest extends TestCase
{
    public function testInvokeCase1() : void
    {
        $input = [
            'data'     => [
                'type'       => 'object',
                'required'   => true,
                'example'    => '',
                'items'      => null,
                'validators' => null,
                'properties' => [
                    'ids' => [
                        'type'       => 'array',
                        'required'   => true,
                        'example'    => '',
                        'items'      => [
                            'type'       => 'string',
                            'required'   => true,
                            'example'    => '',
                            'items'      => null,
                            'validators' => [['assert' => 'assertValidator', 'params' => ['assertName' => 'integerish']]],
                            'properties' => null
                        ],
                        'validators' => null,
                        'properties' => null
                    ]
                ]
            ],
            'headers'    => [],
            'properties' => [
                'path' => 'some_path',
                'type' => 'string',
            ],
        ];
        $inputDataMap = (new InputDataMapAssembler())($input);
        $this->assertInstanceOf(InputDataMap::class, $inputDataMap);
        $this->assertSame(['path' => 'some_path', 'type' => 'string',], $inputDataMap->properties);
        $this->assertSame([], $inputDataMap->headers);
        $this->assertInstanceOf(ValidationRule::class, $inputDataMap->data);
        $this->assertSame('object', $inputDataMap->data->type);
        $this->assertNull($inputDataMap->data->validators);
        $this->assertNull($inputDataMap->data->items);
        $this->assertIsArray($inputDataMap->data->properties);

        $properties = $inputDataMap->data->properties['ids'] ?? null;
        $this->assertInstanceOf(ValidationRule::class, $properties);
        $this->assertSame('array', $properties->type);
        $this->assertInstanceOf(ValidationRule::class, $properties->items);
    }

    public function testInvokeCase2() : void
    {
        $input = [
            'data' => [
                'type'           => 'object',
                'required'       => true,
                'example'        => '',
                'items'          => null,
                'validators'     => null,
                'randomProperty' => null
            ],
            'headers' => [],
            'properties' => [
                'path' => 'some_path',
                'type' => 'string',
            ],
        ];
        $this->expectWarning();
        (new InputDataMapAssembler())($input);
    }
}
