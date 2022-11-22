<?php

declare(strict_types=1);

namespace ESB\Validation\Route;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ESB\Exception\ValidationException;
use ESB\Service\CustomValidatorsPool;
use ReflectionClass;
use Throwable;

use function implode;

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
class InputDataMapValidator
{
    public function __construct(private readonly CustomValidatorsPool $pool)
    {
    }

    /** @psalm-param array{
     *     data: validationRuleRow,
     *     headers: string[],
     *     properties: string[]
     * } $row
     */
    public function validate(array $row, string $propertyPath = 'root') : void
    {
        try {
            Assertion::isArray($row['data'] ?? null, 'InputDataMap::data expected been array');
            Assertion::isArray($row['headers'] ?? null, 'InputDataMap::headers expected been array');
            Assertion::isArray($row['properties'] ?? null, 'InputDataMap::properties expected been array');

            $data = $row['data'] ?? null;
            Assertion::inArray($data['type'] ?? '', ['object', 'array'], 'InputDataMap:on top level row could be array|object only');
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
        $this->validateRow($data, $propertyPath);
    }

    /** @psalm-param validationRuleRow $row */
    private function validateRow(array $row, string $propertyPath) : void
    {
        try {
            Assertion::keyExists($row, 'type', 'InputDataMap::type field should present');
            Assertion::keyExists($row, 'required', 'InputDataMap::required field should present');
            Assertion::keyExists($row, 'example', 'InputDataMap::example field should present');
            Assertion::keyExists($row, 'validators', 'InputDataMap::validators field should present');
            Assertion::keyExists($row, 'items', 'InputDataMap::items field should present');
            Assertion::keyExists($row, 'properties', 'InputDataMap::properties field should present');

            Assertion::inArray(
                $row['type'] ?? null,
                ['object', 'array', 'int', 'float', 'string', 'bool'],
                'InputDataMap:type object|array|int|float|string|bool',
            );
            Assertion::boolean($row['required'] ?? null, 'InputDataMap::required field expected been boolean');
            Assertion::string($row['example'], 'InputDataMap::example field expected been string');
            switch (true) {
                case $row['type'] === 'object':
                    Assertion::notEmpty($row['properties'] ?? null, 'InputDataMap::properties required for row.type = object');
                    Assertion::isArray($row['properties'], 'InputDataMap::properties required for row.type = object');
                    break;
                case $row['type'] === 'array':
                    Assertion::notEmpty($row['items'] ?? null, 'InputDataMap::items required for row.type = array');
                    Assertion::isArray($row['items'], 'InputDataMap::items required for row.type = array');
                    break;
            }
            $validators = $row['validators'] ?? null;
            Assertion::nullOrIsArray($validators, 'InputDataMap::validators field could be array or null');
            $assertReflection = new ReflectionClass(Assertion::class);
            if ($validators) {
                foreach ($validators as $validatorRow) {
                    /** will throw ESBException in case of wrong assert */
                    $assertReflection->hasMethod($validatorRow['assert']) || $this->pool->get($validatorRow['assert']);

                    Assertion::isArray($validatorRow, 'InputDataMap::validators expected each row kind [assertion => assert, properties => []] ');
                    Assertion::notEmpty($validatorRow['assert'], 'InputDataMap::validators.assert expected non-empty string');
                    Assertion::string($validatorRow['assert'], 'InputDataMap::validators.assert expected non-empty string');
                    Assertion::isArray($validatorRow['params'], 'InputDataMap::validators.params expected array');
                }
            }
        } catch (Throwable $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }

        $this->validateItems($row['items'], $propertyPath);
        $this->validateProperties($row['properties'], $propertyPath);
    }

    /** @psalm-param null|validationRuleRow $items */
    private function validateItems(?array $items, string $propertyPath) : void
    {
        if (! $items) {
            return;
        }
        $propertyPath = implode('.', [$propertyPath, 'items']);
        $this->validateRow($items, $propertyPath);
    }

    /** @psalm-param null|array<string, validationRuleRow> $properties */
    private function validateProperties(?array $properties, string $propertyPath) : void
    {
        if (! $properties) {
            return;
        }
        $propertyPath = implode('.', [$propertyPath, 'properties']);
        foreach ($properties as $key => $value) {
            $propertyPath = implode('.', [$propertyPath, $key]);
            $this->validateRow($value, $propertyPath);
        }
    }
}
