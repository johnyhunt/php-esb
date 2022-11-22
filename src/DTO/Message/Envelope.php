<?php

declare(strict_types=1);

namespace ESB\DTO\Message;

use function array_merge;
use function array_values;

class Envelope
{
    /** @var array @psalm-var */
    private array $stamps = [];

    public function __construct(public readonly Message $message, StampInterface ...$stamps)
    {
        foreach ($stamps as $stamp) {
            $this->stamps[$stamp::class] = $stamp;
        }
    }

    public function withStamp(StampInterface $stamp) : self
    {
        return new self($this->message, ...array_merge([$stamp], array_values($this->stamps)));
    }

    public function getStamp(string $stampClass) : ?StampInterface
    {
        return $this->stamps[$stampClass] ?? null;
    }
}
