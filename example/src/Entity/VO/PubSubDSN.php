<?php

declare(strict_types=1);

namespace Example\Entity\VO;

use Assert\Assertion;
use ESB\Entity\VO\AbstractDSN;

use function explode;
use function preg_match;
use function sprintf;
use function strtoupper;

class PubSubDSN extends AbstractDSN
{
    private const PATTERN = '/(\w+%s){2}\w+/';

    public readonly string $client;

    /** like pubsub;topic-name;subscription-name;generateDocument */
    public function __construct(
      public readonly string $topic,
      public readonly string $subscription,
      public readonly string $action,
    ) {
        $this->client = 'PUBSUB';
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true(! ! preg_match(sprintf(static::PATTERN, static::DSN_SEPARATOR), $dsn), 'QueueDSN: dsn string invalid');
        $items = [$client, $topic, $subscription, $action] = explode(static::DSN_SEPARATOR, $dsn);
        Assertion::allString($items);
        Assertion::true(strtoupper($client) === 'PUBSUB', 'PubSubDSN: expecting pubsub as client string');

        return new self($topic, $subscription, $action);
    }
}
