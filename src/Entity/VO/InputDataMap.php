<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

use JsonSerializable;
use function get_object_vars;

class InputDataMap implements JsonSerializable
{
    public function __construct(public readonly ?ValidationRule $data = null, public array $headers = [], public array $properties = [])
    {
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
