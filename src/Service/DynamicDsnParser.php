<?php

declare(strict_types=1);

namespace ESB\Service;

use Assert\AssertionFailedException;
use ESB\DTO\IncomeData;
use ESB\Entity\VO\AbstractDSN;
use ESB\Exception\RouteConfigException;
use Twig\Environment;
use function sprintf;

class DynamicDsnParser implements DynamicDsnParserInterface
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function __invoke(IncomeData $data, AbstractDSN $dsn) : AbstractDSN
    {
        $template = $this->twig->createTemplate($dsn->dsn());

        try {
            return $dsn::fromString($template->render($data->jsonSerialize()));
        } catch (AssertionFailedException $e) {
            throw new RouteConfigException(sprintf('Wrong parameter "%s" for DynamicDsnParser', $e->getMessage()));
        }
    }
}
