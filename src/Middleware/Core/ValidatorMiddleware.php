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
use ESB\Exception\ESBException;
use ESB\Exception\ValidationException;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Validation\AssertValidator;
use ESB\Validation\ValidatorInterface;
use ReflectionClass;

use function implode;

class ValidatorMiddleware implements ESBMiddlewareInterface
{
    /** @psalm-param array<string, ValidatorInterface> $customValidators */
    public function __construct(private readonly array $customValidators = [])
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        if ($validationRulesMap = $route->fromSystemData()->data) {
            $this->validate($data->incomeData->body, $validationRulesMap, 'body');
        }

        return $handler->handle($data, $route);
    }

    /** @throws ValidationException */
    private function validate(mixed $row, ValidationRule $rule, string $propertyPath) : void
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
                case 'bool':
                    $this->validateRow($row, $rule, $propertyPath);
                    break;
                default:
                    throw new ESBException('ValidatorMiddleware:validate unknown rule type');
            }
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $e->getPropertyPath());
        }
    }

    /** @throws AssertionFailedException|ESBException */
    private function validateRow(mixed $row, ValidationRule $rule, string $propertyPath) : void
    {
        $assertionReflection = new ReflectionClass(Assertion::class);
        /** @psalm-var Validator $validator */
        foreach ($rule->validators as $validator) {
            $customValidator = $this->customValidators[$validator->assert] ?? null;
            $validation = match (true) {
                $assertionReflection->hasMethod($validator->assert) => new AssertValidator($validator->assert),
                $customValidator !== null                           => new $customValidator,
                default                                             => throw new ESBException('ValidatorMiddleware::validateRow wrong validation config'),
            };
            $validation->validate($row, $propertyPath, $validator->params);
        }
    }

    /** @throws  AssertionFailedException */
    private function validateObject(array $row, ValidationRule $rule, string $propertyPath) : void
    {
        $this->validateRow($row, $rule, $propertyPath);
        if (! $properties = $rule->properties) {
            Assertion::true(false, 'Properties required for type object', $propertyPath);
        }
        if (! $row && ! $rule->required) {
            return;
        }
        foreach ($properties as $key => $property) {
            $rowValue     = $row[$key] ?? null;
            $propertyPath = implode('.', [$propertyPath, $key]);
            $this->validate($rowValue, $property, $propertyPath);
        }
    }

    /** @throws  AssertionFailedException */
    private function validateArray(array $row, ValidationRule $rule, string $propertyPath) : void
    {
        $this->validateRow($row, $rule, $propertyPath);
        $itemsRule = $rule->items;
        Assertion::notEmpty($itemsRule, 'Items required for type array');
        foreach ($row as $rowValue) {
            $propertyPath = implode('.', [$propertyPath, 'items']);
            $this->validateRow($rowValue, $itemsRule, $propertyPath);
        }
    }
}
