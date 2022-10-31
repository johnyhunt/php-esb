<?php

namespace ESB\Middleware;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\DTO\TargetRequest;
use ESB\Entity\Route;
use ESB\Exception\StopProcessingException;
use ESB\Repository\SyncRecordRepositoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Source;

class ProcessingMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly SyncRecordRepositoryInterface $recordRepository, private readonly Environment $twig)
    {
    }

    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        // If exist sync settings, need check before sending data actuality of them
        if ($settings = $route->syncSettings()) {
            $prevSyncedRecord = $this->recordRepository->findByPk($route->syncSettings()->table(), '---');

            if ($settings->syncOnExist() === false) {
                throw new StopProcessingException();
            }
        }

        /** TODO could be empty string new TargetRequest('') */
        if (! $route->toSystemData()->template) {
            return $handler->handle($data, $route);
        }

        try {
            $template = $this->twig->load($route->toSystemData()->template);
        } catch (LoaderError) {
            $this->twig->parse($this->twig->tokenize(new Source($route->toSystemData()->template, '')));
            $template = $this->twig->createTemplate($route->toSystemData()->template);
        }

        return $handler->handle(
            new RouteData(
                $data->incomeData,
                new TargetRequest($template->render(
                    [
                        'body'    => $data->incomeData->body,
                        'headers' => $data->incomeData->headers,
                        'params'  => $data->incomeData->params,
                    ]
                ))
            ),
            $route
        );
    }
}
