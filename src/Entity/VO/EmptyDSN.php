<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

class EmptyDSN extends AbstractDSN
{
    public static function fromString(string $dsn): static
    {
        Assertion::true($dsn === '', 'EmptyDSN should be empty string');

        return new self();
    }
}
