<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use ESB\Exception\ESBException;
use function sprintf;

class ValidationRule
{
    /** type one of [string,int,object,array,float,bool] */
    public function __construct(
        public readonly string $type,
        public readonly bool $required = false,
        /** @psalm-var array<array-key, Validator> $validators */
        public readonly array $validators = [],
        public readonly ?ValidationRule $items = null,
        /** @psalm-var null|array<string, ValidationRule> $properties */
        public readonly ?array $properties = null,
        public readonly ?array $example = null,
    ) {
    }

    public function propertyByKey(string $key) : ValidationRule
    {
        if ($this->properties && $validationRule = $this->properties[$key] ?? null) {
            return $validationRule;
        }

        throw new ESBException(sprintf('ValidationRule: no property provided for key %s', $key));
    }
}
