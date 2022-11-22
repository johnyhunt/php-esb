<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Exception\ESBException;
use ESB\Validation\ValidatorInterface;

use function sprintf;

class CustomValidatorsPool
{
    /** @psalm-var array<string, ValidatorInterface> $validators  */
    private array $validators = [];

    public function add(string $alias, ValidatorInterface $validator) : void
    {
        $this->validators[$alias] = $validator;
    }

    public function get(string $alias) : ValidatorInterface
    {
        return $this->validators[$alias] ?? throw new ESBException(sprintf('CustomValidatorsPool - no validator matches %s', $alias));
    }
}
