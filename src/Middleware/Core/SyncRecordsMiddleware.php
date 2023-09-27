<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\Entity\Route;
use ESB\Entity\SyncRecord;
use ESB\Exception\RouteConfigException;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Repository\SyncRecordRepositoryInterface;
use Twig\Environment;

use function array_merge;

class SyncRecordsMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly SyncRecordRepositoryInterface $recordRepository, private readonly Environment $twig)
    {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        // If isn't set sync settings or request wasn't success - skip sync stage
        $settings = $route->syncSettings();
        if (! $settings || ! $data->targetResponse()->isSuccess) {
            return $handler->handle($data, $route);
        }

        $fetchSource = ['clientResponse' => $data->targetResponse()->content, 'incomeRequest' => $data->incomeData->jsonSerialize()];

        $toId = '';
        if ($settings->responsePkPath()) {
            $toId = $this->twig->createTemplate($settings->responsePkPath())->render($fetchSource) ?: throw new RouteConfigException('Invalid syncSettings::responsePkPath or result is non-success');
        }
        if (! $syncRecord = $data->syncRecord?->updateRecord($data->targetRequest()->body, $toId)) {
            $fromId     = $this->twig->createTemplate($settings->pkPath())->render(array_merge($data->incomeData->jsonSerialize(), ['clientResponse' => $data->targetResponse()->content])) ?: throw new RouteConfigException('Invalid syncSettings::pkPath');
            $syncRecord = new SyncRecord($fromId, $toId, $data->targetRequest()->body);
        }

        $this->recordRepository->store($settings->table(), $syncRecord);

        // Start sync
        return $handler->handle($data, $route);
    }
}
