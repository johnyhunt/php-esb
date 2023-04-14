<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\DTO\IncomeData;
use ESB\Entity\VO\AbstractDSN;

interface DynamicDsnParserInterface
{
    public function __invoke(IncomeData $data, AbstractDSN $dsn) : AbstractDSN;
}
