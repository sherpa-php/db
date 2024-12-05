<?php

namespace Sherpa\Db\database;

class DatabaseQuery extends Query
{
    public function get(array $columns = ["*"]): array
    {
        $sql = $this->sql();
        $parameters = $this->parameters;

        return json_decode(json_encode(DB::run($sql, $parameters)));
    }
}