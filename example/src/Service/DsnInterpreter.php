<?php

declare(strict_types=1);

namespace Example\Service;

use Assert\Assertion;
use ESB\Assembler\DsnInterpreterInterface;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\EmptyDSN;
use ESB\Entity\VO\HttpDSN;
use Exception;

use function preg_match;
use function strtoupper;

class DsnInterpreter implements DsnInterpreterInterface
{
    public function __invoke(string $dsn) : AbstractDSN
    {
        if ($dsn === '') {
            return new EmptyDSN();
        }
        $matches = [];
        preg_match('/^[a-zA-Z]+/', $dsn, $matches);
        $client = $matches[0] ?? null;
        Assertion::string($client, 'DsnInterpreter: expecting client in dsn been string value');

        return match (strtoupper($client)) {
            HttpDSN::CODE => HttpDSN::fromString($dsn),
            default       => throw new Exception('DsnInterpreter: unknown client')
        };
    }
}
