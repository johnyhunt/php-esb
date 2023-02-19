<?php

namespace ESB\Middleware\Core;

use ESB\CoreHandlerInterface;
use ESB\DTO\ProcessingData;
use ESB\DTO\TargetRequest;
use ESB\Entity\Route;
use ESB\Exception\DuplicateRecordException;
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
                $settings->table(),
                (new ArrayFetch($data->incomeData->jsonSerialize()))($settings->pkPath())
            );

            /** no update, due to settings, available - so exit */
            if ($settings->syncOnExist() === false && $prevSyncedRecord) {
                throw new DuplicateRecordException();
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

        /** We implement spaceless on template content to remove all special chars.
         * Code within try block will work only with FilesystemLoader. By default, we expect template code passed from toSystemData
         */
        try {
            /** TODO is template nullable? */
            $content  = $this->twig->getLoader()->getSourceContext($route->toSystemData()->template() ?? '')->getCode();
            $template = $this->twig->createTemplate((new Spaceless($content))());
        } catch (LoaderError) {
            /** TODO is template nullable? */
            $this->twig->parse($this->twig->tokenize(new Source($route->toSystemData()->template() ?? '', '')));
            $template = $this->twig->createTemplate(
                (new Spaceless($route->toSystemData()->template() ?? ''))()
            );
        }

        $content = $template->render($data->incomeData->jsonSerialize() + ['properties' => $route->fromSystemData()->properties]);

        // nothing to update, same content passed, duplicate call
        if ($content === $data->syncRecord?->requestBody()) {
            throw new DuplicateRecordException();
        }

        return $handler->handle(
            $data->withTargetRequest(new TargetRequest($content, $route->toSystemData()->headers())),
            $route
        );
    }
}
