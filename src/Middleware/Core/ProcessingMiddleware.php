<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\DTO\TargetRequest;
use ESB\Entity\Route;
use ESB\Exception\StopProcessingException;
use ESB\Middleware\ESBMiddlewareInterface;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Repository\SyncRecordRepositoryInterface;
use ESB\Utils\ArrayFetch;
use ESB\Utils\Spaceless;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Source;

class ProcessingMiddleware implements ESBMiddlewareInterface
{
    public function __construct(
        private readonly SyncRecordRepositoryInterface $recordRepository,
        private readonly Environment $twig,
        private readonly RouteRepositoryInterface $routeRepository,
    ) {
    }

    public function process(ProcessingData $data, Route $route, CoreHandlerInterface $handler) : ProcessingData
    {
        // If exist sync settings, need check before sending data actuality of them
        if ($settings = $route->syncSettings()) {
            $prevSyncedRecord = $this->recordRepository->findByPk(
                $route->syncSettings()->table(),
                (new ArrayFetch($data->incomeData->jsonSerialize()))($route->syncSettings()->pkPath())
            );

            if ($settings->syncOnExist() === false && $prevSyncedRecord) {
                throw new StopProcessingException();
            }

            $data = $data->withSyncData($prevSyncedRecord);

            if ($prevSyncedRecord && $updateRouteId = $settings->updateRouteId()) {
                $route = $this->routeRepository->get($updateRouteId);
            }
        }

        if (! $route->toSystemData()->template()) {
            return $handler->handle(
                $data->withTargetRequest(new TargetRequest('', $route->toSystemData()->headers())),
                $route,
            );
        }

        try {
            $template = $this->twig->load($route->toSystemData()->template());
        } catch (LoaderError) {
            $this->twig->parse($this->twig->tokenize(new Source($route->toSystemData()->template(), '')));
            $template = $this->twig->createTemplate($route->toSystemData()->template());
        }

        $content = (new Spaceless(
            $template->render(
                [
                    'body'    => $data->incomeData->body,
                    'headers' => $data->incomeData->headers,
                    'params'  => $data->incomeData->params,
                ]
            )
        ))();

        // duplicate call check
        if ($content === $data->syncRecord?->requestBody()) {
            throw new StopProcessingException();
        }

        return $handler->handle(
            $data->withTargetRequest(new TargetRequest($content, $route->toSystemData()->headers())),
            $route
        );
    }
}
