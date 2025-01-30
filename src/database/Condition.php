<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class Condition implements ConditionInterface
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

    public function statement(): ConditionStatementResponse
    {
        $parameters = [];

        if ($this->value instanceof Reference)
        {
            $value = $this->value->reference;
        }
        else
        {
            $value = '?';
            $parameters[] = $this->value;
        }

        $statement = "$this->column "
                   . "$this->comparisonOperator "
                   . "$value";

        return new ConditionStatementResponse($statement, $parameters);
    }
}