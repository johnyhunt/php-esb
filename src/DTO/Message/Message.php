<?php

declare(strict_types=1);

namespace ESB\DTO\Message;

use Assert\Assertion;
use JsonSerializable;
use function get_object_vars;
use function json_decode;

class Message implements JsonSerializable
{
    public function __construct(public readonly string $body, public readonly string $action, public readonly array $attributes)
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
        Assertion::keyExists($data, 'action', 'Message::deserialize expected message contain action');
        Assertion::string($data['action'], 'Message::deserialize expected action been string');
        Assertion::keyExists($data, 'attributes', 'Message::deserialize expected message contain attributes');
        Assertion::isArray($data['attributes'], 'Message::deserialize expected attributes been array');

        return new self($data['body'], $data['action'], $data['attributes']);
    }
}
