<?php

declare(strict_types=1);

namespace Example\Clients;

use ESB\Client\EsbClientInterface;
use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\ServerDSN;
use ESB\Exception\ESBException;
use Example\Service\ResponseDecodeService;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class HttpClient implements EsbClientInterface
{
    private Client $client;

    public function __construct(private readonly ResponseDecodeService $responseDecodeService, $config = [])
    {
        $this->client = new Client($config);
    }

    public function send(AbstractDSN $dsn, TargetRequest $targetRequest, string $responseFormat) : TargetResponse
    {
        if (! $dsn instanceof ServerDSN) {
            throw new ESBException('Http client expects dsn been ServerDSN instance');
        }
        $requestTime = 0;
        $response    = $this->client->request(
            $dsn->method,
            $dsn->path,
            [
                'headers'  => $targetRequest->headers,
                'body'     => $targetRequest->body,
                'on_stats' => function (TransferStats $stats) use (& $requestTime) {
                    $requestTime = $stats->getTransferTime();
                }
            ],
        );

        return new TargetResponse(
            ($this->responseDecodeService)($response, $responseFormat),
            $requestTime,
            $response->getStatusCode() === 200,
            $response->getStatusCode(),
            $response->getHeaders(),
        );
    }

    public function dsnMatchClass() : string
    {
        return ServerDSN::class;
    }
}
