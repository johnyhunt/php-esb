<?php

declare(strict_types=1);

namespace Example\Assembler;

use Assert\Assertion;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;

/** @psalm-type validationRuleRow = array{type: string, required: bool, validators: null|array, items: null|array, properties: null|array, example: null|string} */
class InputDataMapAssembler
{
    public function __invoke(array $data) : InputDataMap
    {
        Assertion::inArray($data['type'] ?? null, ['object', 'array'], 'InputDataMapAssembler:on top level row could be array|object only');

        return new InputDataMap(
            $this->buildRow($data)
        );
    }

    /** TODO improve validation */
    private function validateRow(array $row) : void
    {
        Assertion::inArray(
            $data['type'] ?? null,
            ['object', 'array', 'int', 'float', 'string', 'bool'],
            'InputDataMapAssembler:on top level row could be array|object only',
        );
        Assertion::nullOrIsArray($row['validators']);
    }

    /** @psalm-param validationRuleRow $data */
    private function buildRow(array $data) : ValidationRule
    {
        $this->validateRow($data);
        [
            'type'       => $type,
            'required'   => $required,
            'validators' => $validators,
            'items'      => $items,
            'properties' => $properties,
            'example'    => $example
        ] = $data;

        return new ValidationRule(
            type: $type,
            required:  $required,
            validators:  $this->buildValidators($required, $type, $validators),
            items:  $this->buildItemsValidation($items),
            properties:  $this->buildPropertiesValidation($properties),
            example:  $example,
        );
    }

    /**@psalm-param null|array<array-key, array{assert: string, properties: array}> $validators
     *
     * @psalm-return null|array<array-key, Validator>
     */
    private function buildValidators(bool $required, string $type, ?array $validators) : ?array
    {
        if (! $validators) {
            return null;
        }
        $resultValidators = [];
        if ($required) {
            $resultValidators[] = new Validator('notEmpty');
        }
        $resultValidators[] = match ($type) {
            'int'    => new Validator('integer'),
            'bool'   => new Validator('boolean'),
            'float'  => new Validator('float'),
            'string' => new Validator('string'),
            default  => null,
        };
        foreach ($validators as $row) {
            Assertion::string($row['assert'], 'InputDataMapAssembler::buildValidators assert field required string');
            Assertion::nullOrIsArray($row['properties'], 'InputDataMapAssembler::buildValidators properties field required array or null');
            $resultValidators = new Validator($row['assert'], $row['properties']);
        }

        return $resultValidators;
    }

    /** @psalm-param null|validationRuleRow $items */
    private function buildItemsValidation(?array $items) : ?ValidationRule
    {
        if (! $items) {
            return null;
        }
        $this->validateRow($items);

        return $this->buildRow($items);
    }

    /** @psalm-param null|array<string, validationRuleRow> $properties */
    private function buildPropertiesValidation(?array $properties) : ?array
    {
        if (! $properties) {
            return null;
        }
        $result = [];
        foreach ($properties as $key => $value) {
            $result[$key] = $this->buildRow($value);
        }

        return $result;
    }
}
