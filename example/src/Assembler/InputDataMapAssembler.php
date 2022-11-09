<?php

declare(strict_types=1);

namespace Example\Assembler;

use Assert\Assertion;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;

use function array_filter;
use function array_values;

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

    private function validateRow(array $row) : void
    {
        Assertion::inArray(
            $row['type'] ?? null,
            ['object', 'array', 'int', 'float', 'string', 'bool'],
            'InputDataMapAssembler:type object|array|int|float|string|bool',
        );
        Assertion::boolean($row['required'] ?? null, 'InputDataMapAssembler::required field expected been boolean');
        Assertion::string($row['example'], 'InputDataMapAssembler::example field expected been string');
        switch (true) {
            case $row['type'] === 'object':
                Assertion::notEmpty($row['properties'] ?? null, 'InputDataMapAssembler::properties required for row.type = object');
                Assertion::isArray($row['properties'], 'InputDataMapAssembler::properties required for row.type = object');
                break;
            case $row['type'] === 'array':
                Assertion::notEmpty($row['items'] ?? null, 'InputDataMapAssembler::items required for row.type = array');
                Assertion::isArray($row['items'], 'InputDataMapAssembler::items required for row.type = array');
                break;
        }
        Assertion::keyExists($row, 'validators', 'InputDataMapAssembler::validators field should present');
        $validators = $row['validators'] ?? null;
        Assertion::nullOrIsArray($validators, 'InputDataMapAssembler::validators field could be array or null');
        if ($validators) {
            foreach ($validators as $validatorRow) {
                Assertion::isArray($validatorRow,'InputDataMapAssembler::validators expected each row kind [assertion => assert, properties => []] ');
                Assertion::notEmpty($validatorRow['assert'], 'InputDataMapAssembler::validators.assert expected non-empty string');
                Assertion::string($validatorRow['assert'], 'InputDataMapAssembler::validators.assert expected non-empty string');
                Assertion::isArray($validatorRow['params'], 'InputDataMapAssembler::validators.params expected array');
            }
        }
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
    private function buildValidators(bool $required, string $type, ?array $validators) : array
    {
        $resultValidators = [];
        if ($required) {
            $resultValidators[] = new Validator('notEmpty', ['message' => "Empty body"]);
        }
        $resultValidators[] = match ($type) {
            'int'    => new Validator('integer'),
            'bool'   => new Validator('boolean'),
            'float'  => new Validator('float'),
            'string' => new Validator('string'),
            default  => null,
        };
        foreach ($validators ?? [] as $row) {
            $resultValidators[] = new Validator($row['assert'], $row['params']);
        }

        return array_values(array_filter($resultValidators));
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
