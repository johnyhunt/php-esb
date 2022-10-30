<?php

declare(strict_types=1);

namespace ESB\DTO;

use ESB\Exception\ESBException;

class RouteData
{
    public function __construct(
        public readonly IncomeData $incomeData,
        public readonly ?TargetRequest $targetRequest = null,
        public readonly ?TargetResponse $targetResponse = null,
    ) {
    }

    public function withTargetResponse(TargetResponse $targetResponse) : self
    {
        return new RouteData(
            incomeData: $this->incomeData,
            targetRequest: $this->targetRequest,
            targetResponse: $targetResponse
        );
    }

    public function targetRequest() : TargetRequest
    {
        if (! $this->targetRequest) {
            throw new ESBException("TargetRequest must be setup on this stage");
        }
        return $this->targetRequest;
    }

    public function targetResponse() : TargetResponse
    {
        if (! $this->targetResponse) {
            throw new ESBException("TargetResponse must be setup on this stage");
        }
        return $this->targetResponse;
    }
}
