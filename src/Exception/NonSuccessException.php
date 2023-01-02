<?php

namespace ESB\Exception;

use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use Exception;

class NonSuccessException extends Exception
{
    private const EXCEPTION_MSG = "Request to target system wasn't success";

    public function __construct(
        public readonly Route $route,
        public readonly ProcessingData $data
    ) {
        parent::__construct(self::EXCEPTION_MSG);
    }
}
