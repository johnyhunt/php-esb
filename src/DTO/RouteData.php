<?php

declare(strict_types=1);

namespace ESB\DTO;

class RouteData
{
    public function __construct(
        public readonly IncomeData $incomeData,
        public readonly ?TargetRequest $targetRequest = null,
        public readonly ?TargetResponse $targetResponse = null,
        public readonly ?TransactionEntity $transactionEntity = null,
    ) {
    }
}
