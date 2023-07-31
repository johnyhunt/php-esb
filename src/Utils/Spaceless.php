<?php

declare(strict_types=1);

namespace ESB\Utils;

use function preg_replace;

class Spaceless
{
    public function __construct(private readonly string $content)
    {
    }

    public function __invoke() : string
    {
        $spacesCut = preg_replace('/\s\s+|\\n/', ' ', $this->content);

        return preg_replace('/\\\t|\\\r|\0|\x0B/', '', $spacesCut);
    }
}
