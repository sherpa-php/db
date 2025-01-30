<?php

namespace Sherpa\Db\database\structure;

class ConditionStatementResponse
{
    public private(set) string $statement;
    public private(set) array $parameters;

    public function __construct(string $statement,
                                array $parameters = [])
    {
        $this->statement = $statement;
        $this->parameters = $parameters;
    }
}