<?php

declare(strict_types=1);

namespace ESB\Exception;

use Exception;
use Throwable;

class ValidationException extends Exception
{
    private string $propertyPath;

    public function __construct(string $message, string $propertyPath, int $code = 0, ?Throwable $previous = null)
    {
        $this->propertyPath = $propertyPath;

        parent::__construct($message, $code, $previous);
    }

    public function propertyPath() : string
    {
        return $this->propertyPath;
    }
}
