<?php

declare(strict_types=1);

namespace ESB\Middleware\Core;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;
use ESB\Exception\RouteConfigException;
use ESB\Exception\ValidationException;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Service\ValidatorsPool;

use function implode;

class ValidatorMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly ValidatorsPool $validators)
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        if ($validationRulesMap = $route->fromSystemData()->data) {
            $this->validate($data->incomeData->body, $validationRulesMap, 'body');
        }

        return $handler->handle($data, $route);
    }

    /** @throws RouteConfigException */
    public function validate(mixed $row, ValidationRule $rule, string $propertyPath) : void
    {
        try {
            switch ($rule->type) {
                case 'array':
                    $this->validateArray($row, $rule, $propertyPath);
                    break;
                case 'object':
                    $this->validateObject($row, $rule, $propertyPath);
                    break;
                case 'int':
                case 'float':
                case 'string':
                case 'scalar':
                case 'numeric':
                case 'bool':
                    $this->validateRow($row, $rule, $propertyPath);
                    break;
                default:
                    throw new RouteConfigException('ValidatorMiddleware:validate unknown rule type');
            }
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $e->getPropertyPath() ?? 'unknown PropertyPath');
        }
    }

    /** @throws AssertionFailedException */
    private function validateRow(mixed $row, ValidationRule $rule, string $propertyPath) : void
    {
        switch (true) {
            case $rule->type == 'int' && $rule->required:
                Assertion::notBlank($row, 'Value required', $propertyPath);
                Assertion::integer($row, 'Value expected integer', $propertyPath);
                break;
            case $rule->type == 'int':
                Assertion::nullOrInteger($row, 'Value expected integer', $propertyPath);
                break;
            case $rule->type == 'float' && $rule->required:
                Assertion::notBlank($row, 'Value required', $propertyPath);
                Assertion::float($row, 'Value expected float', $propertyPath);
                break;
            case $rule->type == 'float':
                Assertion::nullOrFloat($row, 'Value expected float', $propertyPath);
                break;
            case $rule->type == 'numeric' && $rule->required:
                Assertion::notBlank($row, 'Value required', $propertyPath);
                Assertion::numeric($row, 'Value expected numeric', $propertyPath);
                break;
            case $rule->type == 'numeric':
                Assertion::nullOrNumeric($row, 'Value expected numeric', $propertyPath);
                break;
            case $rule->type == 'scalar' && $rule->required:
                Assertion::notBlank($row, 'Value required', $propertyPath);
                Assertion::scalar($row, 'Value expected scalar', $propertyPath);
                break;
            case $rule->type == 'scalar':
                Assertion::nullOrScalar($row, 'Value expected scalar', $propertyPath);
                break;
            case $rule->type == 'string' && $rule->required:
                Assertion::notBlank($row, 'Value required', $propertyPath);
                Assertion::string($row, 'Value expected string', $propertyPath);
                break;
            case $rule->type == 'string':
                Assertion::nullOrString($row, 'Value expected string', $propertyPath);
                break;
            case $rule->type == 'bool' && $rule->required:
                Assertion::boolean($row, 'Value expected boolean', $propertyPath);
                break;
            case $rule->type == 'bool':
                Assertion::nullOrBoolean($row, 'Value expected boolean', $propertyPath);
                break;
        }

        /** @psalm-var Validator $validator */
        foreach ($rule->validators ?? [] as $validator) {
            $validation = $this->validators->get($validator->assert);
            $validation->validate($row, $propertyPath, $validator->params);
        }
    }

    /** @throws  AssertionFailedException */
    private function validateObject(?array $row, ValidationRule $rule, string $propertyPath) : void
    {
        if (! $rule->properties) {
            Assertion::true(false, 'Properties required for type object', $propertyPath);
        }
        if (! $row && ! $rule->required) {
            return;
        }
        $this->validateRow($row, $rule, $propertyPath);
        foreach ($rule->properties ?? [] as $key => $property) {
            $rowValue        = $row[$key] ?? null;
            $newPropertyPath = implode('.', [$propertyPath, $key]);
            $this->validate($rowValue, $property, $newPropertyPath);
        }
    }

    /** @throws  AssertionFailedException */
    private function validateArray(?array $row, ValidationRule $rule, string $propertyPath) : void
    {
        if (! $row && ! $rule->required) {
            return;
        }
        $this->validateRow($row, $rule, $propertyPath);
        $itemsRule = $rule->items;
        Assertion::notEmpty($itemsRule, 'Items required for type array', $propertyPath);
        Assertion::notEmpty($row, 'Row data required to been an array', $propertyPath);
        foreach ($row as $key => $rowValue) {
            $newPropertyPath = implode('.', [$propertyPath, $key]);
            $this->validate($rowValue, $itemsRule, $newPropertyPath);
        }
    }
}
