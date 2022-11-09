<?php

declare(strict_types=1);

namespace ESB\Validation;

use Assert\Assertion;
use BadMethodCallException;
use ESB\Exception\ValidationException;

class AssertValidator implements ValidatorInterface
{
    public function __construct(private readonly string $assertName) {
    }

    public function validate(mixed $value, string $propertyPath, array $params = []) : void
    {
        try {
            Assertion::{$this->assertName}($value, ...$params);
        } catch (BadMethodCallException $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
    }
}
