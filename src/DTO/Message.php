<?php

declare(strict_types=1);

namespace ESB\DTO;

use Assert\Assertion;
use ESB\Entity\VO\AbstractDSN;
use JsonSerializable;

use function json_decode;

/** TODO do we need other properties, except action? */
class Message implements JsonSerializable
{
    private ?object $nativeMessage = null;
    private ?AbstractDSN $routingDsn = null;

    public function __construct(public readonly string $body, public readonly string $action, public readonly array $attributes)
    {
    }

    public function jsonSerialize() : array
    {
        return [
            'body'    => $this->body,
            'action'  => $this->action,
            'headers' => $this->attributes,
        ];
    }

    public static function deserialize(string $message) : self
    {
        Assertion::isJsonString($message, 'Message::deserialize expected json-string');
        $data = json_decode($message, true);
        Assertion::keyExists($data, 'body', 'Message::deserialize expected message contain body');
        Assertion::string($data['body'], 'Message::deserialize expected body been string');
        Assertion::keyExists($data, 'action', 'Message::deserialize expected message contain action');
        Assertion::string($data['action'], 'Message::deserialize expected action been string');
        Assertion::keyExists($data, 'headers', 'Message::deserialize expected message contain headers');
        Assertion::isArray($data['headers'], 'Message::deserialize expected headers been array');

        return new self($data['body'], $data['action'], $data['headers']);
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

    public function injectRoutingDsn(AbstractDSN $dsn) : void
    {
        $this->routingDsn = $dsn;
    }

    public function routingDsn() : ?AbstractDSN
    {
        return $this->routingDsn;
    }
}
