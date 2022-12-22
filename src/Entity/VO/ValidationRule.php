<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use JsonSerializable;

use function get_object_vars;

class ValidationRule implements JsonSerializable
{
    /** type one of [string,int,object,array,float,bool] */
    public function __construct(
        public readonly string $type,
        public readonly bool $required = false,
        /** @psalm-var null|array<array-key, Validator> $validators */
        public readonly ?array $validators = null,
        public readonly ?ValidationRule $items = null,
        /** @psalm-var null|array<string, ValidationRule> $properties */
        public readonly ?array $properties = null,
        public readonly ?string $example = null,
    ) {
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
