<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Client\EsbClientInterface;
use ESB\Entity\VO\AbstractDSN;
use ESB\Exception\SetupException;

use function sprintf;

class ClientPool
{
    /** @psalm-var array<string, EsbClientInterface> $clients  */
    private array $clients = [];

    public function __construct(EsbClientInterface ...$clients)
    {
        foreach ($clients as $client) {
            if ($this->clients[$client->dsnMatchClass()] ?? null) {
                throw new SetupException(sprintf('ClientPool invalid setup, dsn matches more than 1 client, %s', $client->dsnMatchClass()));
            }
            $this->clients[$client->dsnMatchClass()] = $client;
        }
    }

    public function get(AbstractDSN $dsn) : EsbClientInterface
    {
        return $this->clients[$dsn::class] ?? throw new SetupException(sprintf('ClientPool - no client matches dsn %s', $dsn::class));
    }
}
