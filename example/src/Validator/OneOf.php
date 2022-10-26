<?php

declare(strict_types=1);

namespace Example\Validator;

use Assert\Assertion;
use ESB\Validation\ValidatorInterface;

class OneOf implements ValidatorInterface
{

    public function validate(mixed $value, array $params = []) : void
    {
        Assertion::notEmpty($params, 'OneOf::expected array of 2 keys');

        [$firstKey, $secondKey] = $params;

        Assertion::string($firstKey);
        Assertion::keyExists($value, $firstKey, 'OneOf::firstKey invalid');

        Assertion::string($secondKey);
        Assertion::keyExists($value, $secondKey, 'OneOf::firstKey invalid');

        Assertion::false($value[$firstKey] && $value[$secondKey], 'OneOf::values could not be set both');
        Assertion::true($value[$firstKey] || $value[$secondKey], 'OneOf::values could not be empty both');
    }
}
