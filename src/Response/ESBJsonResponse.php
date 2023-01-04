<?php

declare(strict_types=1);

namespace ESB\Response;

use InvalidArgumentException;
use JsonSerializable;
use Nyholm\Psr7\MessageTrait;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

use function gettype;
use function in_array;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function sprintf;

use const JSON_ERROR_NONE;

class ESBJsonResponse implements ResponseInterface
{
    use MessageTrait;

    private const DEFAULT_JSON_FLAGS = 79;

    /**
     * @var array Map of standard HTTP status code/reason phrases
     */
    private const PHRASES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * @psalm-suppress NoInterfaceProperties
     * @psalm-param mixed $body
     */
    public function __construct(
        array|JsonSerializable $body,
        private int $statusCode = 200,
        array $headers = [],
        string $version = '1.1',
        private string $reasonPhrase = '',
        private int $encodingOptions = self::DEFAULT_JSON_FLAGS
    ) {
        $jsonBody         = $this->jsonEncode($body, $encodingOptions);
        $this->stream     = Stream::create($jsonBody);
        $this->setHeaders(['content-type' => 'application/json'] + $headers);

        $this->reasonPhrase ?: $this->reasonPhrase = self::PHRASES[$this->statusCode] ?? '';
        $this->protocol = $version;
    }

    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase() : string
    {
        return $this->reasonPhrase;
    }

    public function withStatus($code, $reasonPhrase = '') : self
    {
        if (! in_array(gettype($code), ['integer'])) {
            throw new InvalidArgumentException('Status code has to be an integer');
        }

        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException('Status code has to be an integer between 100 and 599');
        }

        $new               = clone $this;
        $new->statusCode   = $code;
        $new->reasonPhrase = $reasonPhrase ?: self::PHRASES[$new->statusCode] ?? 'Unknown';

        return $new;
    }

    private function jsonEncode(array|JsonSerializable $data, int $encodingOptions) : string
    {
        /** @psalm-suppress UnusedFunctionCall */
        json_encode(null); // Clear json_last_error()

        $json = json_encode($data, $encodingOptions);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to encode data to JSON in %s: %s',
                    self::class,
                    json_last_error_msg()
                )
            );
        }

        return $json;
    }
}
