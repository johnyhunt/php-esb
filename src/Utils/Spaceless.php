<?php

declare(strict_types=1);

namespace ESB\Utils;

class Spaceless
{
    public function __construct(private readonly string $content)
    {
    }

    public function __invoke() : string
    {
        return preg_replace('/\s\s+|\t|\n|\r|\0|\x0B/', '', $this->content);
    }
}
