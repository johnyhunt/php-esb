<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use function get_object_vars;
use function implode;

abstract class AbstractDSN
{
    public const DSN_SEPARATOR = ';';

    abstract public static function fromString(string $dsn) : static;

    public function dsn() : string
    {
        return implode(static::DSN_SEPARATOR, get_object_vars($this));
    }
}
