<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\Entity\Route;
use ESB\Entity\SyncRecord;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Repository\SyncRecordRepositoryInterface;

class SyncRecordsMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly SyncRecordRepositoryInterface $recordRepository)
    {
    }

    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        // If isn't set sync settings - skip this step
        if (! $route->syncSettings()) {
            return $handler->handle($data, $route);
        }

        if ($syncRecord = $data->syncRecord) {
            $syncRecord->updateRecord($data->targetRequest()->body);
        } else {
            $syncRecord = new SyncRecord('', '', $data->targetRequest()->body);
        }

        $this->recordRepository->store($route->syncSettings()->table(), $syncRecord);

        // Start sync
        return $handler->handle($data, $route);
    }
}
