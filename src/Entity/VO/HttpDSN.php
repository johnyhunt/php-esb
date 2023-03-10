<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

use function explode;
use function preg_match;
use function sprintf;
use function strtoupper;

/**
 * @psalm-consistent-constructor
 */
class HttpDSN extends AbstractDSN
{
    protected const PATTERN = '/(\w|\/+%s){2}\w+/';

    public const CODE = 'HTTP';

    protected string $client;

    public function __construct(
        public readonly string $method,
        public readonly string $path,
    ) {
        $this->client = static::CODE;
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true(! ! preg_match(sprintf(static::PATTERN, static::DSN_SEPARATOR), $dsn), 'DSN string invalid');

        $items = [$client, $method, $path] = explode(static::DSN_SEPARATOR, $dsn);
        Assertion::allString($items);
        Assertion::true(strtoupper($client) === static::CODE, 'Expecting string as client');

        return new static(strtoupper($method), $path);
    }
}
