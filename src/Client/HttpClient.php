<?php

declare(strict_types=1);

namespace ESB\Client;

use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\ServerDSN;
use ESB\Exception\ESBException;
use ESB\Service\ResponseDecodeService;
use GuzzleHttp\Client;

class HttpClient implements EsbClientInterface
{
    private Client $client;

    public function __construct(private readonly ResponseDecodeService $responseDecodeService, $config = [])
    {
        $this->client = new Client($config);
    }

    public function send(AbstractDSN $dsn, TargetRequest $targetRequest) : TargetResponse
    {
        if (! $dsn instanceof ServerDSN) {
            throw new ESBException('Http client expects dsn been ServerDSN instance');
        }
        $response = $this->client->request($dsn->method, $dsn->path, ['headers' => $targetRequest->headers, 'body' => $targetRequest->body]);

        return new TargetResponse(
            ($this->responseDecodeService)($response),
            $response->getStatusCode(),
            $response->getHeaders(),
        );
    }

    public function dsnMatchClass() : string
    {
        return ServerDSN::class;
    }
}
