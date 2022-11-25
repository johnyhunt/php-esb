<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use ESB\Assembler\RouteEntityAssembler;
use ESB\Entity\Route;
use ESB\Exception\ESBException;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Response\ESBJsonResponse;
use ESB\Validation\Route\RouteEntityInputValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function is_array;
use function reset;
use function strtoupper;

class RouteCRUDHandler
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly RouteEntityInputValidator $validator,
        private readonly RouteEntityAssembler $assembler,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return match (strtoupper($request->getMethod())) {
            'POST'   => $this->create($request),
            'GET'    => $this->read($request),
            'PUT'    => $this->update($request),
            'DELETE' => $this->delete($request),
        };
    }

    private function create(ServerRequestInterface $request) : ResponseInterface
    {
        $requestData = $request->getParsedBody();
        if (! is_array($requestData)) {
            throw new ESBException('RouteCRUDHadler - wrong request body');
        }
        $this->validator->validate($requestData);
        $route = $this->assembler->buildRoute($requestData);
        $this->routeRepository->store($route);

        return new ESBJsonResponse(['message' => 'Successfully processed']);
    }

    private function read(ServerRequestInterface $request) : ResponseInterface
    {
        $name  = $request->getAttribute('name');
        $route = array_filter($this->routeRepository->loadAll(), fn(Route $route) => $route->name() === $name);

        return new ESBJsonResponse(reset($route));
    }

    private function update(ServerRequestInterface $request) : ResponseInterface
    {
        $requestData = $request->getParsedBody();
        if (! is_array($requestData)) {
            throw new ESBException('RouteCRUDHadler - wrong request body');
        }
        $this->validator->validate($requestData);
        $route = $this->assembler->buildRoute($requestData);
        $this->routeRepository->store($route);

        return new ESBJsonResponse(['message' => 'Successfully processed']);
    }

    private function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $name  = $request->getAttribute('name');
        $route = array_filter($this->routeRepository->loadAll(), fn(Route $route) => $route->name() === $name);
        if (! $route) {
            throw new ESBException('Unknown route');
        }
        $this->routeRepository->delete(reset($route));

        return new ESBJsonResponse(['message' => 'Successfully deleted']);
    }
}
