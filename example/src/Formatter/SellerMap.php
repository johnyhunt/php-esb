<?php

declare(strict_types=1);

namespace Example\Formatter;

class SellerMap
{
    /** example of injecting some service, which could get data from external sources, like database or whatever */
    public function __construct()
    {
    }

    /** could be some complicated logic to fetch required data  */
    public function __invoke(string $key) : array
    {
        return ['any_calculated_key'];
    }
}
