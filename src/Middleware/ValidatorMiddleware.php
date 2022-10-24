<?php

declare(strict_types=1);

namespace ESB\Middleware;

use Assert\Assertion;
use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\Entity\Route;
use ESB\Entity\VO\ValidationRule;
use ESB\Entity\VO\Validator;
use ESB\Exception\ESBException;
use ESB\Validation\AssertValidator;
use ESB\Validation\ValidatorInterface;
use ReflectionClass;

class ValidatorMiddleware implements ESBMiddlewareInterface
{
    /** @psalm-var array<string, ValidatorInterface> $customValidators */
    private array $customValidators = [];

    public function addCustomValidator(string $key, ValidatorInterface $validator) : void
    {
        $this->customValidators[$key] = $validator;
    }

    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        $this->validate($data->incomeData->body, $route->fromSystemData()->data);

        return $handler->handle($data, $route);
    }

    private function validate(mixed $row, ValidationRule $rule) : void
    {
        switch ($rule->type) {
            case 'array':
                $this->validateArray($row, $rule);
                break;
            case 'object':
                $this->validateObject($row, $rule);
                break;
            case 'int':
            case 'float':
            case 'string':
            case 'bool':
                $this->validateRow($row, $rule);
                break;
            default:
                throw new ESBException('ValidatorMiddleware:validate unknown rule type');
        }
    }

    private function validateRow(mixed $row, ValidationRule $rule) : void
    {
        $assertionReflection = new ReflectionClass(Assertion::class);
        /** @psalm-var Validator $validator */
        foreach ($rule->validators as $validator) {
            $customValidator = $this->customValidators[$validator->assert] ?? null;
            $validation = match (true) {
                $assertionReflection->hasMethod($validator->assert) => new AssertValidator($validator->assert, $validator->params),
                $customValidator !== null                           => new $customValidator,
                default                                             => throw new ESBException('ValidatorMiddleware::validateRow wrong validation config'),
            };
            $validation->validate($row);
        }
    }

    private function validateObject(array $row, ValidationRule $rule) : void
    {
        if (! $properties = $rule->properties) {
            throw new ESBException('ValidatorMiddleware:validateObject properties for type object should be set');
        }
        foreach ($properties as $key => $property) {
            $rowValue = $row[$key] ?? null;
            $this->validate($rowValue, $property);
        }
    }

    private function validateArray(array $row, ValidationRule $rule) : void
    {
        $itemsRule = $rule->items;
        Assertion::notEmpty($itemsRule, 'ValidatorMiddleware::for row type = array items required');
        foreach ($row as $rowValue) {
            $this->validateRow($rowValue, $itemsRule);
        }
    }
}
