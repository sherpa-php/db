<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class ConditionGroup
{
    public private(set) object $methods;
    public private(set) Operator $operator;

    public function __construct(callable $group,
                                Operator $operator)
    {
        $this->methods = (object)[
            "group" => $group,
        ];
        $this->operator = $operator;
    }
}