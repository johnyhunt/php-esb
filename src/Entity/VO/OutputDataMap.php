<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class OutputDataMap
{
    public function __construct(public readonly ?string $template)
    {
    }
}
