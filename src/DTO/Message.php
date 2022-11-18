<?php

declare(strict_types=1);

namespace ESB\DTO;

use Assert\Assertion;
use JsonSerializable;

use function get_object_vars;
use function json_decode;

class Message implements JsonSerializable
{
    private ?object $nativeMessage   = null;

    public function __construct(public readonly string $body, public readonly string $xroute, public readonly array $attributes)
    {
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }

    public static function deserialize(string $message) : self
    {
        Assertion::isJsonString($message, 'Message::deserialize expected json-string');
        $data = json_decode($message, true);
        Assertion::keyExists($data, 'body', 'Message::deserialize expected message contain body');
        Assertion::string($data['body'], 'Message::deserialize expected body been string');
        Assertion::keyExists($data, 'xroute', 'Message::deserialize expected message contain xroute');
        Assertion::string($data['xroute'], 'Message::deserialize expected xroute been string');
        Assertion::keyExists($data, 'headers', 'Message::deserialize expected message contain headers');
        Assertion::isArray($data['headers'], 'Message::deserialize expected headers been array');

        return new self($data['body'], $data['xroute'], $data['headers']);
    }

    public function injectNativeMessage(object $nativeMessage) : self
    {
        $this->nativeMessage = $nativeMessage;

        return $this;
    }

    public function nativeMessage() : object
    {
        return $this->nativeMessage;
    }
}
