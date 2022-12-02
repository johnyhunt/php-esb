<?php

declare(strict_types=1);

namespace Example\Validation;

use Assert\Assertion;
use ESB\Validation\ValidatorInterface;

class OneOf implements ValidatorInterface
{

    public function validate(mixed $value, string $propertyPath, array $params = []) : void
    {
        Assertion::isArray($params['rows'] ?? null, 'OneOf::expected array of 2 keys', propertyPath: $propertyPath);
        Assertion::notEmpty($params['rows'], 'OneOf::expected array of 2 keys', propertyPath: $propertyPath);

        [$firstKey, $secondKey] = $params['rows'];

        Assertion::string($firstKey, propertyPath: $propertyPath);
        Assertion::keyExists($value, $firstKey, 'OneOf::firstKey invalid', propertyPath: $propertyPath);

        Assertion::string($secondKey, propertyPath: $propertyPath);
        Assertion::keyExists($value, $secondKey, 'OneOf::firstKey invalid');

        Assertion::false($value[$firstKey] && $value[$secondKey], 'OneOf::values could not be set both');
        Assertion::true($value[$firstKey] || $value[$secondKey], 'OneOf::values could not be empty both');
    }
}
