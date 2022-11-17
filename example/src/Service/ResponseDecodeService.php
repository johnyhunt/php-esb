<?php

declare(strict_types=1);

namespace Example\Service;

use Assert\Assertion;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function json_decode;
use function json_encode;
use function simplexml_load_string;

class ResponseDecodeService
{
    public function __invoke(ResponseInterface $response, string $responseFormat) : array
    {
        try {
            $responseContent = $response->getBody()->getContents();
            switch ($responseFormat)
            {
                case 'xml':
                    $xml = simplexml_load_string($responseContent);
                    Assertion::true($xml !== false);

                    return json_decode(json_encode($xml), true);
                case 'json':
                    Assertion::isJsonString($responseContent);

                    return json_decode($responseContent, true);
                default:
                    return [];
            }
        } catch (Throwable) {
            return [];
        }
    }
}
