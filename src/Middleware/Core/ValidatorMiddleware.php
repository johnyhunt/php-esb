<?php

declare(strict_types=1);

namespace ESB\Middleware\Core;

use Assert\Assertion;
use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;
use ESB\Exception\ESBException;
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
            $this->validate($data->incomeData->body, $validationRulesMap, 'root');
        }

        return $handler->handle($data, $route);
    }

    private function validate(mixed $row, ValidationRule $rule, string $propertyPath) : void
    {
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
    }

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

    private function validateObject(array $row, ValidationRule $rule, string $propertyPath) : void
    {
        $this->validateRow($row, $rule, $propertyPath);
        if (! $properties = $rule->properties) {
            throw new ESBException('ValidatorMiddleware:validateObject properties for type object should be set');
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

    private function validateArray(array $row, ValidationRule $rule, string $propertyPath) : void
    {
        $this->validateRow($row, $rule, $propertyPath);
        $itemsRule = $rule->items;
        Assertion::notEmpty($itemsRule, 'ValidatorMiddleware::for row type = array items required');
        foreach ($row as $rowValue) {
            $propertyPath = implode('.', [$propertyPath, 'items']);
            $this->validateRow($rowValue, $itemsRule, $propertyPath);
        }
    }
}
