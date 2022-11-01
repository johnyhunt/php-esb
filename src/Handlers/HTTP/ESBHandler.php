<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use ESB\CoreRunner;
use ESB\CoreRunnerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Response\ESBJsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function date;

class ESBHandler
{
    /** @psalm-param array<string, CoreRunnerInterface> $coreRunnerList */
    public function __construct(
        private readonly array $coreRunnerList,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        /** @psalm-var Route $route */
        $route = $request->getAttribute(Route::class);
        /** @psalm-var ProcessingData $routeData */
        $routeData = $request->getAttribute(ProcessingData::class);

        // Choose runner for process request/message
        $customRunner = $this->coreRunnerList[$route->customRunner()] ?? null;

        /** @psalm-var CoreRunnerInterface $runner */
        $runner = match (true) {
            $customRunner !== null => $customRunner,
            default                => $this->coreRunnerList[CoreRunner::class]
        };

        $result = $runner->runCore($routeData, $route);

        return new ESBJsonResponse(
            [
                'date' => date('Y-m-s H:i:s'),
                'data' => $routeData->incomeData,
                'targetRequest' => $result->targetRequest()->body
            ]
        );
    }
}
