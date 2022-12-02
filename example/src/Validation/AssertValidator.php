<?php

declare(strict_types=1);

namespace Example\Validation;

use Assert\Assertion;
use BadMethodCallException;
use ESB\Exception\ValidationException;
use ESB\Validation\ValidatorInterface;

class AssertValidator implements ValidatorInterface
{
    public function validate(mixed $value, string $propertyPath, array $params = []) : void
    {
        $params['propertyPath'] = $propertyPath;
        $assertName             = $params['assertName'] ?? '';
        unset($params['assertName']);
        try {
            Assertion::{$assertName}($value, ...$params);
        } catch (BadMethodCallException $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
    }
}
