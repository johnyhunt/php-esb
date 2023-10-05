<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

use function array_filter;
use function explode;
use function get_object_vars;
use function implode;
use function preg_match;
use function sprintf;
use function strtoupper;

/**
 * @psalm-consistent-constructor
 */
class HttpDSN extends AbstractDSN
{
    protected const PATTERN = '/(\w|\/+%s){2,3}\w+/';

    public const CODE = 'HTTP';

    protected string $client;

    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly ?string $host = null,
    ) {
        $this->client = static::CODE;
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true(! ! preg_match(sprintf(static::PATTERN, static::DSN_SEPARATOR), $dsn), 'DSN string invalid');

        $list  = explode(static::DSN_SEPARATOR, $dsn);
        $items = [$client, $method, $path] = $list;
        $host  = $list[3] ?? null;
        Assertion::allString($items);
        Assertion::true(strtoupper($client) === static::CODE, 'Expecting string as client');

        return new static(strtoupper($method), $path, $host);
    }

    public function dsn() : string
    {
        return implode(static::DSN_SEPARATOR, array_filter(get_object_vars($this)));
    }
}
