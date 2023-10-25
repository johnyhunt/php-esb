<?php

declare(strict_types=1);

namespace ESB\DTO;

use ESB\Entity\SyncRecord;
use RuntimeException;

class ProcessingData
{
    public function __construct(
        public readonly IncomeData $incomeData,
        public readonly ?TargetRequest $targetRequest = null,
        public readonly ?TargetResponse $targetResponse = null,
        public readonly ?SyncRecord $syncRecord = null,
    ) {
    }

    public function withSyncData(?SyncRecord $syncRecord) : self
    {
        if (! $syncRecord) {
            return $this;
        }

        return new self(
            incomeData: $this->incomeData,
            targetRequest: $this->targetRequest,
            targetResponse: $this->targetResponse,
            syncRecord: $syncRecord,
        );
    }

    public function withTargetRequest(TargetRequest $targetRequest) : self
    {
        return new self(
            incomeData: $this->incomeData,
            targetRequest: $targetRequest,
            syncRecord: $this->syncRecord,
        );
    }

    public function withTargetResponse(TargetResponse $targetResponse) : self
    {
        return new self(
            incomeData: $this->incomeData,
            targetRequest: $this->targetRequest,
            targetResponse: $targetResponse,
            syncRecord: $this->syncRecord
        );
    }

    public function targetRequest() : TargetRequest
    {
        if (! $this->targetRequest) {
            throw new RuntimeException("TargetRequest must be setup on this stage");
        }
        return $this->targetRequest;
    }

    public function targetResponse() : TargetResponse
    {
        if (! $this->targetResponse) {
            throw new RuntimeException("TargetResponse must be setup on this stage");
        }
        return $this->targetResponse;
    }
}
