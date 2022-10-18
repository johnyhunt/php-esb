<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use Assert\Assertion;

use function explode;
use function preg_match;

class QueueDSN extends AbstractDSN
{
    /** like e-invoice:pubsub:einvoice:generateDocument */
    public function __construct(
      public readonly string $client,
      public readonly string $topic,
      public readonly string $action,
    ) {
    }

    public static function fromString(string $dsn) : static
    {
        Assertion::true(! ! preg_match('/(\w+:){2}\w+/', $dsn), 'QueueDSN: dsn string invalid');
        $items = [$client, $topic, $action] = explode(':', $dsn);
        Assertion::allString($items);
        Assertion::true($client === 'pubsub', 'QueueDSN: expecting pubsub as client string');

        return new self($client, $topic, $action);
    }
}
