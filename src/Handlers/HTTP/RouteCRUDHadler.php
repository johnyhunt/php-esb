<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use ESB\Assembler\RouteEntityAssembler;
use ESB\Exception\ESBException;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Response\ESBJsonResponse;
use ESB\Validation\Route\RouteEntityInputValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;
use function strtoupper;

class RouteCRUDHadler
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly RouteEntityInputValidator $validator,
        private readonly RouteEntityAssembler $assembler,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        if (strtoupper($request->getMethod()) === 'DELETE') {
            $name  = $request->getAttribute('name');
            $route = $this->routeRepository->get($name);
            $this->routeRepository->delete($route);

            return new ESBJsonResponse(['message' => 'Successfully deleted']);
        }
        if (strtoupper($request->getMethod()) === 'GET') {
            $name  = $request->getAttribute('name');
            $route = $this->routeRepository->get($name);

            return new ESBJsonResponse($route);
        }
        $requestData = $request->getParsedBody();
        if (! is_array($requestData)) {
            throw new ESBException('RouteCRUDHadler - wrong request body');
        }
        $this->validator->validate($requestData);
        $route = $this->assembler->buildRoute($requestData);
        if (! $this->routeRepository->checkConsistence($route)) {
            throw new ESBException('Provided route is inconsistent, check used foreign keys');
        }
        $this->routeRepository->store($route);

        return new ESBJsonResponse(['message' => 'Successfully processed']);
    }
}
