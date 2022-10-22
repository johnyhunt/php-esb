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
        public readonly ?TransactionEntity $transactionEntity = null,
    ) {
    }

    public function targetRequest(): TargetRequest
    {
        if (! $this->targetRequest) {
            throw new ESBException("Request must be setup on this stage");
        }
        return $this->targetRequest;
    }
}
