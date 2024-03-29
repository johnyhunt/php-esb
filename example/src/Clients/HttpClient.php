<?php

declare(strict_types=1);

namespace Example\Clients;

use ESB\Client\EsbClientInterface;
use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\IntegrationSystem;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\HttpDSN;
use Example\Service\ResponseDecodeService;
use Nyholm\Psr7\Response;
use RuntimeException;

use function curl_close;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function fopen;
use function fseek;
use function fwrite;

use const CURLINFO_HTTP_CODE;
use const CURLOPT_HTTPGET;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

class HttpClient implements EsbClientInterface
{
    private $client;

    public function __construct(private readonly ResponseDecodeService $responseDecodeService)
    {
        /** just as example, use any http-client on your own, like guzzle or similar  */
        $this->client = new class {
            public function request(string $method, string $path, string $body, array $headers) : Response
            {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $normalizedHeaders = [];
                foreach ($headers as $key => $value) {
                    $normalizedHeaders[] = $key . ': ' . $value;
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $normalizedHeaders);

                switch ($method) {
                    case 'GET':
                        curl_setopt($ch, CURLOPT_URL,$path . '?' . $body);
                        curl_setopt($ch, CURLOPT_HTTPGET, true);
                        break;
                    case 'POST':
                        curl_setopt($ch, CURLOPT_URL,$path);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                        break;
                    default:
                        throw new RuntimeException('Unknown method');
                }
                if ($method === 'POST') {
                    curl_setopt($ch, CURLOPT_POST, 1);
                }

                if (! $response = curl_exec($ch)) {
                    $errors   = curl_error($ch);
                    $resource = fopen('php://temp', 'r+');
                    fwrite($resource, $errors);
                    fseek($resource, 0);

                    return new Response(curl_getinfo($ch, CURLINFO_HTTP_CODE), body: $resource);
                }

                curl_close($ch);

                $resource = fopen('php://temp', 'r+');
                fwrite($resource, $response);
                fseek($resource, 0);

                return new Response(curl_getinfo($ch, CURLINFO_HTTP_CODE), body: $resource);
            }
        };
    }

    /** targetSystem could be used to determine whether TargetResponse is success */
    public function send(AbstractDSN $dsn, TargetRequest $targetRequest, IntegrationSystem $targetSystem, string $responseFormat) : TargetResponse
    {
        if (! $dsn instanceof HttpDSN) {
            throw new RuntimeException('Http client expects dsn been ServerDSN instance');
        }
        $requestTime = 0;
        $response    = $this->client->request(
            $dsn->method,
            $dsn->path,
            $targetRequest->body,
            $targetRequest->headers,
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
        return HttpDSN::class;
    }
}
