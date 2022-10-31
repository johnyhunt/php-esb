<?php

declare(strict_types=1);

namespace ESB\Test\Entity;

use ESB\Entity\SyncTable;
use PHPUnit\Framework\TestCase;

class SyncTableTest extends TestCase
{
    public function testTableName()
    {
        $syncTable = new SyncTable(tableName: "some_orders_table");

        $this->assertSame('some_orders_table', $syncTable->tableName());
    }
}
