<?php

declare(strict_types=1);

namespace Example\Service;

use Assert\Assertion;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function json_decode;
use function json_encode;
use function simplexml_load_string;
use function str_contains;

class ResponseDecodeService
{
    public function __invoke(ResponseInterface $response) : array
    {
        try {
            $contentType     = strtr($response->getHeaderLine('Content-type'), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
            $responseContent = $response->getBody()->getContents();
            switch (true)
            {
                case str_contains($contentType, 'text/xml') || str_contains($contentType, 'application/xml'):
                    $xml = simplexml_load_string($responseContent);
                    Assertion::true($xml !== false);

                    return json_decode(json_encode($xml), true);
                case str_contains($contentType, 'application/json'):
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
