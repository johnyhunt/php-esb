<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\Entity\VO\AbstractDSN;
use ESB\Exception\ESBException;

interface DsnInterpreterInterface
{
    /** @throws ESBException */
    public function __invoke(string $dsn) : AbstractDSN;
}
