<?php

declare(strict_types=1);

namespace Opsway\ESB\Handlers;

use Opsway\ESB\Response\ESBJsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function date;

class ESBHandler
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return new ESBJsonResponse(['date' => date('Y-m-s H:i:s')]);
    }
}
