<?php

namespace ESB\Entity;

class SyncRecord
{
    private readonly int $createdAt;
    private int $updatedAt;

    public function __construct(
        private string $fromId,
        private string $toId,
        private string $requestBody
    ) {
        $this->createdAt = time();
        $this->updatedAt = time();
    }

    public function updateRecord(string $requestHash, string $toId) : self
    {
        $this->toId        = $toId;
        $this->requestBody = $requestHash;
        $this->updatedAt   = time();

        return $this;
    }

    public function fromId() : string
    {
        return $this->fromId;
    }

    public function toId() : string
    {
        return $this->toId;
    }

    public function requestBody() : string
    {
        return $this->requestBody;
    }

    public function createdAt() : int
    {
        return $this->createdAt;
    }

    public function updatedAt() : int
    {
        return $this->updatedAt;
    }
}
