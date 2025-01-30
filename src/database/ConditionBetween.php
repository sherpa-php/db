<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class ConditionBetween implements ConditionInterface
{
    public private(set) string $column;
    public private(set) string|int|float $min;
    public private(set) string|int|float $max;
    public private(set) Operator $operator;

    public function __construct(string $column,
                                string|int|float $min,
                                string|int|float $max,
                                Operator $operator)
    {
        $this->column = $column;
        $this->min = $min;
        $this->max = $max;
        $this->operator = $operator;
    }

    public function statement(): ConditionStatementResponse
    {
        $parameters = [
            $this->min,
            $this->max
        ];

        $statement = "$this->column BETWEEN ? AND ?";

        return new ConditionStatementResponse(
            $statement,
            $parameters);
    }
}