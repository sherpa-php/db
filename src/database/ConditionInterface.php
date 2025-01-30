<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\structure\ConditionStatementResponse;

interface ConditionInterface
{
    public function statement(): ConditionStatementResponse;
}