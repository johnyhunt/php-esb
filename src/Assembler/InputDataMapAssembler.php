<?php

declare(strict_types=1);

namespace ESB\Assembler;

use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;

use function array_map;

/**
 * @psalm-type assert = array{
 *     assert: string,
 *     params: array
 * }
 * @psalm-type validationRuleRow = array{
 *     type: string,
 *     required: bool,
 *     validators: null|array<array-key, assert>,
 *     items: null|validationRuleRow,
 *     properties: null|array<string, validationRuleRow>,
 *     example: null|string
 * }
 */
class InputDataMapAssembler
{
    /** @psalm-param array{
     *     data: validationRuleRow,
     *     headers: string[],
     *     properties: string[]
     * } $row
     */
    public function __invoke(array $row) : InputDataMap
    {
        $data = $row['data'] ?? null;

        return new InputDataMap(
            $this->buildRow($data),
            $row['headers'] ?? [],
            $row['properties'] ?? [],
        );
    }

    /** @psalm-param validationRuleRow $data */
    private function buildRow(array $data) : ValidationRule
    {
        [
            'type'       => $type,
            'required'   => $required,
            'validators' => $validators,
            'items'      => $items,
            'properties' => $properties,
            'example'    => $example,
        ] = $data;

        return new ValidationRule(
            type: $type,
            required:  $required,
            validators:  $this->buildValidators($validators),
            items:  $this->buildItemsValidation($items),
            properties:  $this->buildPropertiesValidation($properties),
            example:  $example,
        );
    }

    /**@psalm-param null|array<array-key, assert> $validators
     * @psalm-return null|array<array-key, Validator>
     */
    private function buildValidators(?array $validators) : array
    {
        if (! $validators) {
            return [];
        }

        return array_map(fn(array $row) => new Validator($row['assert'], $row['params']), $validators);
    }

    /** @psalm-param null|validationRuleRow $items */
    private function buildItemsValidation(?array $items) : ?ValidationRule
    {
        if (! $items) {
            return null;
        }

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
