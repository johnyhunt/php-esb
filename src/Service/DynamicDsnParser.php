<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\DTO\IncomeData;
use ESB\Entity\VO\AbstractDSN;
use ESB\Exception\RouteConfigException;
use ESB\Utils\ArrayFetch;

use function preg_match_all;
use function sprintf;
use function str_replace;

class DynamicDsnParser implements DynamicDsnParserInterface
{
    private const DYNAMIC_PROPERTIES_PATTERN = '/\\{\\{(\w|\\.)+}}/';

    public function __invoke(IncomeData $data, AbstractDSN $dsn): AbstractDSN
    {
        $dsnString = $dsn->dsn();
        preg_match_all(self::DYNAMIC_PROPERTIES_PATTERN, $dsnString, $matches);
        if (! $matches[0] ?? null) {
            return $dsn;
        }
        foreach ($matches[0] as $key) {
            $normalizedKey = str_replace(['{', '}'], '', $key);
            $value         = (new ArrayFetch($data->jsonSerialize()))($normalizedKey) ?? throw new RouteConfigException(sprintf('Unknown %s for DynamicDsnParser', $key));
            $dsnString     = str_replace($key, (string) $value, $dsnString);
        }

        return $dsn::fromString($dsnString);
    }
}
