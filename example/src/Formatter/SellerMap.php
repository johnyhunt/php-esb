<?php

declare(strict_types=1);

namespace Example\Formatter;

use Example\Service\SellerMapRepository;

class SellerMap
{
    /** example of injecting some service, which could get data from external sources, like database or whatever */
    public function __construct(private readonly SellerMapRepository $sellerMapRepository)
    {
    }

    /** could be some complicated logic to fetch required data  */
    public function __invoke(string $key) : array
    {
        return $this->sellerMapRepository->get($key);
    }
}
