<?php

namespace ESB\Middleware;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\DTO\TargetRequest;
use ESB\Entity\Route;
use ESB\Exception\ESBException;
use ESB\Repository\SyncTableRepositoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Source;

class ProcessingMiddleware implements ESBMiddlewareInterface
{
    public function __construct(private readonly SyncTableRepositoryInterface $repository, private readonly Environment $twig)
    {
    }

    public function process(RouteData $data, Route $route, CoreHandlerInterface $handler)
    {
        if ($route->syncTable()?->syncOnExist && $this->repository->wasSynced($data, $route->syncTable())) {
            throw new ESBException('Duplicate request call');
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