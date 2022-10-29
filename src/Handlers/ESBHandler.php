<?php

declare(strict_types=1);

namespace ESB\Handlers;

use ESB\Core;
use ESB\DTO\IncomeData;
use ESB\DTO\RouteData;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Response\ESBJsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Source;
use function date;
use function file_get_contents;
use function implode;
use function strlen;
use function substr;

class ESBHandler implements ESBHandlerInterface
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeProvider,
        private readonly Core                     $coreHandler,
        private readonly Environment              $twig,
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

        $this->coreHandler->run(new RouteData($incomeData), $routeEntity);

        /** TODO ALL code with twig is for debug and should be removed from handler */
        $templateSource = $routeEntity->toSystemData()->template;
        try {
            $template = $this->twig->load($templateSource);
        } catch (LoaderError) {
            $this->twig->parse($this->twig->tokenize(new Source($templateSource, '')));
            $template = $this->twig->createTemplate($templateSource);
        }

        return new ESBJsonResponse(
            [
                'date' => date('Y-m-s H:i:s'),
                'data' => $incomeData,
                'twig' => $template->render([
                    'body'    => $incomeData->body,
                    'headers' => $incomeData->headers,
                    'params'  => $incomeData->params,
                ]),
            ]
        );
    }
}
