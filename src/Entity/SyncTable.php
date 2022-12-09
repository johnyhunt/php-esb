<?php

declare(strict_types=1);

namespace ESB\Entity;

use JsonSerializable;

use function get_object_vars;

class SyncTable implements JsonSerializable
{
    public function __construct(
        private string $tableName,
    ) {
    }

    public function tableName(): string
    {
        return $this->tableName;
    }

    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
