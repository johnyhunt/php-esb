<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class OutputResponse
{
    public function __construct(private readonly string $format, private readonly string $template)
    {
    }

    public function format() : string
    {
        return $this->format;
    }

    public function template() : string
    {
        return $this->template;
    }
}
