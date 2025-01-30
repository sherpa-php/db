<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class ConditionRegex implements ConditionInterface
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

    public function statement(): ConditionStatementResponse
    {
        return new ConditionStatementResponse(
            "$this->column REGEXP ?",
            [$this->regex]);
    }
}