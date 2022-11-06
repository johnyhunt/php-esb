<?php

namespace ESB\Enum;

enum MessageResultEnum
{
    case ACK;
    case REQUEUE;
    case REJECT;
}
