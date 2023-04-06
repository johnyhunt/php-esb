<?php

declare(strict_types=1);

namespace ESB\Handlers\HTTP;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ESBHandlerInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface;
}
