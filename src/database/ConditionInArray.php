<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;

class ConditionInArray
{
    public private(set) string $column;
    public private(set) array $array;
    public private(set) Operator $operator;

    public function __construct(string $column,
                                array $array,
                                Operator $operator)
    {
        $this->column = $column;
        $this->array = $array;
        $this->operator = $operator;
    }
}