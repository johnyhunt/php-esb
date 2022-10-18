<?php

declare(strict_types=1);

namespace ESB\Service;

use Assert\Assertion;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\QueueDSN;
use ESB\Entity\VO\ServerDSN;
use ESB\Exception\ESBException;

use function explode;
use function preg_match;
use function strtoupper;

class DsnInterpreter implements DsnInterpreterInterface
{
    /** @throws ESBException */
    public function __invoke(string $dsn) : AbstractDSN
    {
        Assertion::true(! ! preg_match('/\w+:\w+/', $dsn), 'DsnInterpreter: dsn string invalid');
        [$client] = explode(':', $dsn);
        Assertion::string($client, 'DsnInterpreter: expecting client in dsn been string value');

        return match (strtoupper($client)) {
            'HTTP'   => ServerDSN::fromString($dsn),
            'PUBSUB' => QueueDSN::fromString($dsn),
            default  => throw new ESBException('DsnInterpreter: unknown client')
        };
    }
}
