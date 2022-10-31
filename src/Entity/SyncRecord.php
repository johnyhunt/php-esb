<?php

namespace ESB\Entity;

class SyncRecord
{
    public function __construct(
        private string $fromId,
        private string $toId,
        private string $requestHash
    ) {
        $this->createdAt = time();
        $this->updatedAt = time();
    }

    public function updateRecord(string $requestHash)
    {
        $this->requestHash = $requestHash;
        $this->updatedAt   = time();
    }

    public function fromId(): string
    {
        return $this->fromId;
    }

    public function toId(): string
    {
        return $this->toId;
    }

    public function requestHash(): string
    {
        return $this->requestHash;
    }

    public function createdAt(): int
    {
        return $this->createdAt;
    }

    public function updatedAt(): int
    {
        return $this->updatedAt;
    }
}
