<?php

declare(strict_types=1);

namespace ESB\Assembler;

use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;

use function array_map;

class InputDataMapAssembler
{
    public function __invoke(array $row) : InputDataMap
    {
        return new InputDataMap(
            $this->buildRow($row['data']),
            $row['headers'] ?? [],
            $row['properties'] ?? [],
        );
    }

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

    /**@psalm-return null|array<array-key, Validator>*/
    private function buildValidators(?array $validators) : ?array
    {
        if (! $validators) {
            return null;
        }

        return array_map(fn(array $row) => new Validator($row['assert'], $row['params']), $validators);
    }

    private function buildItemsValidation(?array $items) : ?ValidationRule
    {
        if (! $items) {
            return null;
        }

        return $this->buildRow($items);
    }

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
