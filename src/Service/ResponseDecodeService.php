<?php

declare(strict_types=1);

namespace ESB\Service;

use Assert\Assertion;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use function in_array;
use function json_decode;
use function json_encode;
use function simplexml_load_string;

class ResponseDecodeService
{
    public function __invoke(ResponseInterface $response) : array
    {
        try {
            $contentType     = $response->getHeader('Content-type');
            $responseContent = $response->getBody()->getContents();
            switch (true)
            {
                case in_array('text/xml', $contentType):
                    $xml = simplexml_load_string($responseContent);
                    Assertion::true($xml !== false);

                    return json_decode(json_encode($xml), true);
                case in_array('application/json', $contentType):
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
