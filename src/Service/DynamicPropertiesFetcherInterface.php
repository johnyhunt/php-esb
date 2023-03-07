<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\DTO\IncomeData;

interface DynamicPropertiesFetcherInterface
{
    public function __invoke(IncomeData $data, array $properties) : array;
}
