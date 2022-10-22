<?php

declare(strict_types=1);

namespace ESB\Enum;

enum SystemTransport : string
{
    case SYNC  = 'sync';
    case ASYNC = 'async';
}
