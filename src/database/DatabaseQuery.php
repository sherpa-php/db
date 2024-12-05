<?php

namespace Sherpa\Db\database;

class DatabaseQuery extends Query
{
    public function get(array $columns = ["*"]): array
    {
        $sql = $this->sql();
        $parameters = $this->parameters;

        return DB::run($sql, $parameters);
    }
}