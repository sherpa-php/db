<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;

class ConditionRegex
{
    public private(set) string $column;
    public private(set) string $regex;
    public private(set) Operator $operator;

    public function __construct(string $column,
                                string $regex,
                                Operator $operator)
    {
        $this->column = $column;
        $this->regex = $regex;
        $this->operator = $operator;
    }
}