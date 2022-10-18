<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use function get_object_vars;
use function implode;

abstract class AbstractDSN
{
    abstract public static function fromString(string $dsn) : static;

    public function dsn() : string
    {
        return implode(':', get_object_vars($this));
    }
}
