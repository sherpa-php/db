<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\OrderType;

class Order
{
    public private(set) string $column;
    public private(set) OrderType $orderType;

    public function __construct(string $column,
                                OrderType $orderType)
    {
        $this->column = $column;
        $this->orderType = $orderType;
    }
}