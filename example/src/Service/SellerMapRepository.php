<?php

declare(strict_types=1);

namespace Example\Service;

class SellerMapRepository
{
    public function get(string $id) : array
    {
        return [
            'warehouse' => 'WHE',
            'department' => 'Some department',
        ];
    }
}
