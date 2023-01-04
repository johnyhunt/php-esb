<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

final class EmptyDSN extends AbstractDSN
{
    public function __construct()
    {
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true($dsn === '', 'EmptyDSN should be empty string');

        return new static();
    }
}
