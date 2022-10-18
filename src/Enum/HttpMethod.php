<?php

declare(strict_types=1);

namespace Opsway\ESB\Enum;

enum HttpMethod : string
{
    case POST   = 'POST';
    case GET    = 'GET';
    case PUT    = 'PUT';
    case PATCH  = 'PATCH';
    case DELETE = 'DELETE';
}