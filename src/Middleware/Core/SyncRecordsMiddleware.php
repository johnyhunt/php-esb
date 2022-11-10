<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Entity\SyncRecord;
use ESB\Exception\ESBException;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Repository\SyncRecordRepositoryInterface;
use ESB\Utils\ArrayFetch;
use function array_merge;

class SyncRecordsMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly SyncRecordRepositoryInterface $recordRepository)
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        // If isn't set sync settings - skip this step
        if (! $settings = $route->syncSettings()) {
            return $handler->handle($data, $route);
        }

        $resultFetchSource = ['response' => $data->targetResponse()->content, 'request' => $data->incomeData->jsonSerialize()];

        $toId = (new ArrayFetch($resultFetchSource))($settings->responsePkPath()) ?? throw new ESBException('Invalid syncSettings::responsePkPath or result is non-success');
        if (! $syncRecord = $data->syncRecord?->updateRecord($data->targetRequest()->body)) {
            $fromId     = (new ArrayFetch($data->incomeData->jsonSerialize()))($settings->pkPath()) ?? throw new ESBException('Invalid syncSettings::pkPath');
            $syncRecord = new SyncRecord($fromId, $toId ?? '', $data->targetRequest()->body);
        }

        $this->recordRepository->store($route->syncSettings()->table(), $syncRecord);

        // Start sync
        return $handler->handle($data, $route);
    }
}
