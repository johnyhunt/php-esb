<?php

declare(strict_types=1);

namespace Example\Auth;

use Assert\Assertion;
use ESB\Auth\AuthServiceInterface;
use ESB\DTO\TargetRequest;
use ESB\Entity\VO\HttpDSN;
use ESB\Utils\ArrayFetch;
use Example\Clients\HttpClient;
use Exception;

use function json_encode;

class JsonAuthService implements AuthServiceInterface
{
    public function __construct(private readonly HttpClient $client)
    {
    }

    public function authenticate(TargetRequest $targetRequest, array $settings): void
    {
        $dsn  = HttpDSN::fromString($settings['dsn'] ?? '');
        $data = $settings['data'] ?? null;
        Assertion::isArray($data, 'JsonAuthService::settings::data expected been array');
        $headers = $settings['headers'] ?? [];
        Assertion::isArray($data, 'JsonAuthService::settings::headers expected been array');
        $headers += ['Content-Type' => 'application/json'];

        $token = $settings['token'] ?? null;
        Assertion::string($token, 'JsonAuthService::settings::token expected been non-blank string');
        Assertion::notBlank($token, 'JsonAuthService::settings::token expected been non-blank string');

        $outputTokenName = $settings['output-name'] ?? null;
        Assertion::string($outputTokenName, 'JsonAuthService::settings::output-name expected been non-blank string');
        Assertion::notBlank($outputTokenName, 'JsonAuthService::settings::output-name expected been non-blank string');

        $response = $this->client->send($dsn, new TargetRequest(json_encode($data), $headers), 'json');

        foreach ($response->headers as $key => $value) {
            if ($key === $token) {
                Assertion::string($value, 'JsonAuthService::response - search token-header expected been string');
                $targetRequest->headers += [$outputTokenName => $value];

                return;
            }
        }

        if ($tokenValue = (new ArrayFetch($response->content))($token)) {
            $targetRequest->headers += [$outputTokenName => $tokenValue];

            return;
        }
        throw new Exception('JsonAuthService::authentication failed');
    }
}
