<?php

namespace ESB\Entity;

/** TODO is it really hash(requestHash) or some json|xml body ? */
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

    public function updateRecord(string $requestHash) : self
    {
        $this->requestHash = $requestHash;
        $this->updatedAt   = time();

        return $this;
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
