<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class ConditionRaw implements ConditionInterface
{
    public private(set) string $raw;
    public private(set) Operator $operator;

    public function __construct(string $raw,
                                Operator $operator)
    {
        $this->raw = $raw;
        $this->operator = $operator;
    }

    public function statement(): ConditionStatementResponse
    {
        return new ConditionStatementResponse(
            $this->raw);
    }
}