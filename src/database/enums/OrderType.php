<?php

namespace Sherpa\Db\database\enums;

/**
 * SQL ordering types.
 */
enum OrderType
{
    case ASC;
    case DESC;

    /**
     * Returns OrderType enum value from string.
     *
     * @param string $order
     * @return OrderType
     */
    public static function from(string $order): OrderType
    {
        return match ($order)
        {
            "desc" => self::DESC,
            default => self::ASC,
        };
    }
}