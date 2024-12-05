<?php

namespace Sherpa\Db\database\enums;

enum JoinType
{
    case INNER;
    case LEFT;
    case RIGHT;
    case FULL;
}
