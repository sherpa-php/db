<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;

class ConditionRaw
{
    public private(set) string $raw;
    public private(set) Operator $operator;

    public function __construct(string $raw,
                                Operator $operator)
    {
        $this->raw = $raw;
        $this->operator = $operator;
    }
}