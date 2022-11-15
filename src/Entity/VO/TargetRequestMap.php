<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use JsonSerializable;

use function get_object_vars;

class TargetRequestMap implements JsonSerializable
{
    public function __construct(
        private readonly array $headers = [],
        private readonly ?string $template = null,
        private readonly ?AuthMap $auth = null,
    ) {
    }

    public function headers() : array
    {
        return $this->headers;
    }

    public function auth() : ?AuthMap
    {
        return $this->auth;
    }

    public function template() : ?string
    {
        return $this->template;
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
