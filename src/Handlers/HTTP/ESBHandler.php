<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use ESB\CoreRunner;
use ESB\CoreRunnerInterface;
use ESB\DTO\IncomeData;
use ESB\DTO\RouteData;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Response\ESBJsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function date;
use function implode;
use function strlen;
use function substr;

class ESBHandler implements ESBHandlerInterface
{
    /** @psalm-param array<string, CoreRunnerInterface> $coreRunnerList */
    public function __construct(
        private readonly array                    $coreRunnerList,
        private readonly RouteRepositoryInterface $routeProvider,
        private readonly string                   $basePath = '/middleware',
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $path = substr($request->getUri()->getPath(), strlen($this->basePath));
        $dsn  = implode(';', ['HTTP', strtoupper($request->getMethod()), $path]);

        $routeEntity = $this->routeProvider->get($dsn);
        $incomeData  = new IncomeData(
            $request->getHeaders(),
            $request->getAttributes(),
            $request->getParsedBody() ?: $request->getQueryParams()
        );

        // Choose runner for process request/message
        $customRunner = $this->coreRunnerList[$routeEntity->customRunner()] ?? null;

        /** @psalm-var CoreRunnerInterface $runner */
        $runner = match (true) {
            $customRunner !== null => $customRunner,
            default                => $this->coreRunnerList[CoreRunner::class]
        };

        $runner->runCore(new RouteData($incomeData), $routeEntity);

        return new ESBJsonResponse(
            [
                'date' => date('Y-m-s H:i:s'),
                'data' => $incomeData,
            ]
        );
    }
}
