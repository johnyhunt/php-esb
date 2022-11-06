<?php

declare(strict_types=1);

namespace Example\Service;

use Assert\Assertion;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\PubSubDSN;
use ESB\Entity\VO\ServerDSN;
use ESB\Exception\ESBException;

use function preg_match;
use function strtoupper;

class DsnInterpreter implements DsnInterpreterInterface
{
    public function __invoke(string $dsn) : AbstractDSN
    {
        $matches = [];
        preg_match('/^[a-zA-Z]+/', $dsn, $matches);
        $client = $matches[0] ?? null;
        Assertion::string($client, 'DsnInterpreter: expecting client in dsn been string value');

        return match (strtoupper($client)) {
            'HTTP'   => ServerDSN::fromString($dsn),
            'PUBSUB' => PubSubDSN::fromString($dsn),
            default  => throw new ESBException('DsnInterpreter: unknown client')
        };
    }
}
