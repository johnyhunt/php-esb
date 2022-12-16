<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;
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
        Assertion::inArray($this->type, ['object', 'array', 'int', 'float', 'string', 'bool'], 'ValidationRule:type invalid');
        if ($this->validators) {
            Assertion::allIsInstanceOf($this->validators, Validator::class, 'ValidationRule::validators could be ValidationRule set only');
        }
        switch ($this->type) {
            case 'object':
                Assertion::notEmpty($this->properties, 'ValidationRule::properties required for type = object');
                Assertion::isArray($this->properties, 'ValidationRule::properties required for row.type = object');
                Assertion::allIsInstanceOf($this->properties, ValidationRule::class, 'ValidationRule::properties could be ValidationRule set only');
                break;
            case 'array':
                Assertion::notEmpty($this->items, 'ValidationRule::items required for type = array');
                break;
        }
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
