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

        $fetchSource = ['clientResponse' => $data->targetResponse()->content, 'incomeRequest' => $data->incomeData->jsonSerialize()];

        $toId = '';
        if ($settings->responsePkPath()) {
            $toId = (new ArrayFetch($fetchSource))($settings->responsePkPath()) ?? throw new ESBException('Invalid syncSettings::responsePkPath or result is non-success');
        }
        if (! $syncRecord = $data->syncRecord?->updateRecord($data->targetRequest()->body)) {
            $fromId     = (new ArrayFetch($fetchSource))($settings->pkPath()) ?? throw new ESBException('Invalid syncSettings::pkPath');
            $syncRecord = new SyncRecord($fromId, (string) $toId, $data->targetRequest()->body);
        }

        $this->recordRepository->store($route->syncSettings()->table(), $syncRecord);

        // Start sync
        return $handler->handle($data, $route);
    }
}
