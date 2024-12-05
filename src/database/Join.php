<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\JoinType;

class Join
{
    public private(set) string $table;
    public private(set) array $conditions;
    public private(set) JoinType $joinType;

    public function __construct(string $table,
                                array $conditions,
                                JoinType $joinType)
    {
        $this->table = $table;
        $this->conditions = $conditions;
        $this->joinType = $joinType;
    }
}