<?php

declare(strict_types=1);

namespace ESB\Validation;

use Assert\Assertion;
use Assert\AssertionFailedException;
use BadMethodCallException;
use ESB\Exception\ESBException;

class AssertValidator implements ValidatorInterface
{
    public function __construct(private readonly string $assertName) {
    }

    public function validate(mixed $value, array $params = []) : void
    {
        try {
            Assertion::{$this->assertName}($value, ...$params);
        } catch (BadMethodCallException | AssertionFailedException $exception) {
            throw new ESBException((string) ($this->params['message'] ?? $exception->getMessage()));
        }
    }
}
