<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;

class Condition
{
    public private(set) string $column;
    public private(set) string $comparisonOperator;
    public private(set) mixed $value;
    public private(set) Operator $operator;

    public function __construct(string $column,
                                string $comparisonOperator,
                                mixed $value,
                                Operator $operator)
    {
        $this->column = $column;
        $this->comparisonOperator = $comparisonOperator;
        $this->value = $value;
        $this->operator = $operator;
    }
}