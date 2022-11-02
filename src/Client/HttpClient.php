<?php

declare(strict_types=1);

namespace ESB\Client;

use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\ServerDSN;
use ESB\Exception\ESBException;
use GuzzleHttp\Client;

class HttpClient implements EsbClientInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function send(AbstractDSN $dsn, TargetRequest $targetRequest) : TargetResponse
    {
        if (! $dsn instanceof ServerDSN) {
            throw new ESBException('Http client expects dsn been ServerDSN instance');
        }
        $response = $this->client->request($dsn->method, $dsn->path, ['headers' => $targetRequest->headers, 'body' => $targetRequest->body]);

        return new TargetResponse(
            $response->getBody()->getContents(),
            $response->getStatusCode(),
            $response->getHeaders(),
        );
    }

    public function dsnMatchClass() : string
    {
        return ServerDSN::class;
    }
}
