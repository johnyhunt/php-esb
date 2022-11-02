<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class TargetRequestMap
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
}
