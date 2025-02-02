<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\Query;

class ConditionScopeQuery extends Query
{
    public private(set) array $conditions;

    public function __construct(string $table)
    {
        parent::__construct($table);
    }
}