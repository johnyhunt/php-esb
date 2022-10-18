<?php

declare(strict_types=1);

namespace Opsway\ESB\Entity;

enum SystemTransport : string
{
    case HTTP  = 'http';
    case ASYNC = 'async';
}
