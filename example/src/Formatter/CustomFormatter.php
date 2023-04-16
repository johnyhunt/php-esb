<?php

declare(strict_types=1);

namespace Example\Formatter;

class CustomFormatter
{
    /** example of injecting some service, which could get data from external sources, like a database or whatever */
    public function __construct()
    {
    }

    /** could be some complicated logic to fetch required data  */
    public function __invoke(string $key) : string
    {
        return "text_from_formatter + key $key from template";
    }
}
