<?php

namespace ESB\Exception;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use Exception;
use Throwable;

class NonSuccessException extends Exception
{
    private const EXCEPTION_MSG = "Request to target system wasn't success";

    public function __construct(
        public readonly Route $route,
        public readonly ProcessingData $data,
        Throwable $previous = null,
    ) {
        parent::__construct(self::EXCEPTION_MSG, previous: $previous);
    }
}
