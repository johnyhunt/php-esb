<?php

declare(strict_types=1);

namespace Example\Auth;

use Assert\Assertion;
use ESB\Auth\AuthServiceInterface;
use ESB\DTO\TargetRequest;
use ESB\Entity\VO\ServerDSN;
use ESB\Exception\ESBException;
use ESB\Utils\ArrayFetch;
use Example\Clients\HttpClient;
use function json_decode;
use function json_encode;

class JsonAuthService implements AuthServiceInterface
{
    public function __construct(private readonly HttpClient $client)
    {
    }

    public function authenticate(TargetRequest $targetRequest, array $settings): void
    {
        $dsn  = ServerDSN::fromString($settings['dsn'] ?? '');
        $data = $settings['data'] ?? null;
        Assertion::isArray($data, 'JsonAuthService::settings::data expected been array');
        $headers = $settings['headers'] ?? null;
        Assertion::isArray($data, 'JsonAuthService::settings::headers expected been array');
        $headers += ['Content-Type' => 'application/json'];

        $token = $settings['token'] ?? null;
        Assertion::string($token, 'JsonAuthService::settings::token expected been non-blank string');
        Assertion::notBlank($token, 'JsonAuthService::settings::token expected been non-blank string');

        $outputTokenName = $settings['output-name'] ?? null;
        Assertion::string($outputTokenName, 'JsonAuthService::settings::output-name expected been non-blank string');
        Assertion::notBlank($outputTokenName, 'JsonAuthService::settings::output-name expected been non-blank string');

        $response = $this->client->send($dsn, new TargetRequest(json_encode($data), $headers));

        foreach ($response->headers as $key => $value) {
            if ($key === $token) {
                Assertion::string($value, 'JsonAuthService::response - search token-header expected been string');
                $targetRequest->headers += [$outputTokenName => $value];

                return;
            }
        }
        Assertion::isJsonString($response->content, 'JsonAuthService::response::content expected been json-string');
        $responseBody = json_decode($response->content, true);
        if ($tokenValue = (new ArrayFetch($responseBody))($token)) {
            $targetRequest->headers += [$outputTokenName => $tokenValue];

            return;
        }
        throw new ESBException('JsonAuthService::authentication failed');
    }

    public function matchAlias(): string
    {
        return 'JsonAuthService';
    }
}
