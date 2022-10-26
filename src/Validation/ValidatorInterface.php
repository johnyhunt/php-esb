<?php

declare(strict_types=1);

namespace ESB\Validation;

interface ValidatorInterface
{
    public function validate(mixed $value, array $params = []) : void;
}
