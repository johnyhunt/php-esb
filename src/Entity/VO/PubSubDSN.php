<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

use function explode;
use function preg_match;
use function sprintf;

class PubSubDSN extends AbstractDSN
{
    private const PATTERN = '/(\w+%s){2}\w+/';

    /** like pubsub;topic-name;subscription-name;generateDocument */
    public function __construct(
      public readonly string $client,
      public readonly string $topic,
      public readonly string $subscription,
      public readonly string $action,
    ) {
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true(! ! preg_match(sprintf(static::PATTERN, static::DSN_SEPARATOR), $dsn), 'QueueDSN: dsn string invalid');
        $items = [$client, $topic, $subscription, $action] = explode(static::DSN_SEPARATOR, $dsn);
        Assertion::allString($items);
        Assertion::true($client === 'pubsub', 'PubSubDSN: expecting pubsub as client string');

        return new self($client, $topic, $subscription, $action);
    }
}
