<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

use function explode;
use function preg_match;
use function sprintf;
use function strtoupper;

class ServerDSN extends AbstractDSN
{
    private const PATTERN = '/(\w|\/+%s){2}\w+/';

    /** like outer-system;http;post;/boodmo/sap/dispatch-box */
    public function __construct(
        public readonly string $client,
        public readonly string $method,
        public readonly string $path,
    ) {
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true(! ! preg_match(sprintf(static::PATTERN, static::DSN_SEPARATOR), $dsn), 'ServerDSN: dsn string invalid');

        $items = [$client, $method, $path] = explode(static::DSN_SEPARATOR, $dsn);
        Assertion::allString($items);
        Assertion::true(strtoupper($client) === 'HTTP', 'ServerDSN: expecting http string as client');

        return new self(strtoupper($client), strtoupper($method), $path);
    }
}
