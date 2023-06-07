<?php

declare(strict_types=1);

namespace ESB\Service;

use ESB\DTO\IncomeData;
use ESB\Exception\SetupException;
use Throwable;
use Twig\Environment;

use function is_string;

class DynamicPropertiesFetcher implements DynamicPropertiesFetcherInterface
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function __invoke(IncomeData $data, array $properties) : array
    {
        $result = [];
        try {
            foreach ($properties as $key => $value) {
                if (! is_string($value)) {
                    $result[$key] = $value;

                    continue;
                }
                $template     = $this->twig->createTemplate($value);
                $result[$key] = $template->render($data->jsonSerialize());
            }
        } catch (Throwable $e) {
            throw new SetupException($e->getMessage());
        }

        return $result;
    }
}
