<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\DTO\IncomeData;
use ESB\Utils\ArrayFetch;

use function preg_match;
use function trim;

class DynamicPropertiesFetcher implements DynamicPropertiesFetcherInterface
{
    /** @psalm-param array<string, string|numeric> $properties */
    public function __invoke(IncomeData $data, array $properties) : array
    {
        $result = [];
        foreach ($properties as $key => $value) {
            preg_match('/\\{\\{(.+)}}/', $value, $matches);
            if (! $matches) {
                $result[$key] = $value;
                continue;
            }
            $normalizedValueKey = trim($matches[1] ?? '');
            $result[$key]       = (new ArrayFetch($data->jsonSerialize()))($normalizedValueKey);
        }

        return $result;
    }
}
