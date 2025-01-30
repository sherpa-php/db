<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class ConditionLike implements ConditionInterface
{
    public private(set) string $column;
    public private(set) string $like;
    public private(set) Operator $operator;

    public function __construct(string $column,
                                string $like,
                                Operator $operator)
    {
        $this->column = $column;
        $this->like = $like;
        $this->operator = $operator;
    }

    public function statement(): ConditionStatementResponse
    {
        return new ConditionStatementResponse(
            "$this->column LIKE '$this->like'");
    }
}