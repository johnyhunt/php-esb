<?php

declare(strict_types=1);

namespace ESB\Entity\VO;

class SyncTable
{
    public function __construct(
        public readonly string $tableName,
        public readonly array $tableDataMap, // TODO could be set of vo like boodmo_id => [order.id]
        public readonly array $pkId, // TODO could be set of vo like boodmo_id => [order.id]
        public readonly bool $syncOnExist,
        public readonly bool $syncOnChange,
    ) {
    }
}
