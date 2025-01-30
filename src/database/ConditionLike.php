<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;

class ConditionLike
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
}