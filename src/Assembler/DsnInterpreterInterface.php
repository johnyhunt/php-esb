<?php

declare(strict_types=1);

namespace ESB\Assembler;

use ESB\Entity\VO\AbstractDSN;

interface DsnInterpreterInterface
{
    public function __invoke(string $dsn) : AbstractDSN;
}
