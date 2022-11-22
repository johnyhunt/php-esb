<?php

declare(strict_types=1);

namespace ESB\Validation\Route;

use Assert\Assertion;
use ESB\Exception\ValidationException;
use Throwable;

class IntegrationSystemValidator
{
    /** @psalm-param array{code: string} $row */
    public function validate(array $row, string $propertyPath = 'root') : void
    {
        try {
            Assertion::string($row['code'] ?? null, 'IntegrationSystemValidator::code expected non-blank string');
            Assertion::notBlank($row['code'], 'IntegrationSystemValidator::code expected non-blank string');
        } catch (Throwable $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
    }
}
