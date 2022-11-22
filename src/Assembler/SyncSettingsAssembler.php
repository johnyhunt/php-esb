<?php

declare(strict_types=1);

namespace ESB\Assembler;

use ESB\Entity\SyncTable;
use ESB\Entity\VO\SyncSettings;

class SyncSettingsAssembler
{
    public function __invoke(?array $table, ?array $syncSettings) : ?SyncSettings
    {
        if (! $syncSettings || ! $table) {
            return null;
        }

        return new SyncSettings(
            ...['table' => new SyncTable(...$table)] + $syncSettings
        );
    }
}
