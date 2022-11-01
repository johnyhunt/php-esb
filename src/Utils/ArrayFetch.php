<?php

declare(strict_types=1);

namespace ESB\Utils;

use function array_key_exists;
use function array_reduce;
use function explode;
use function is_array;

class ArrayFetch
{
    public function __construct(private readonly array $data, private readonly string $pathDelimiter = '.')
    {
    }

    public function __invoke(string $pathString) : mixed
    {
        $path = explode($this->pathDelimiter, $pathString);

        return array_reduce(
            $path,
            static fn (mixed $source, string $key) => is_array($source) && array_key_exists($key, $source) ? $source[$key] : null,
            $this->data
        );
    }
}
