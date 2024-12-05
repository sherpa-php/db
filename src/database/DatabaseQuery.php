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

    public function first(array $columns = ["*"]): ?object
    {
        return $this->get($columns)[0] ?? null;
    }

    public function last(array $columns = ["*"]): ?object
    {
        $rows = $this->get($columns);

        return array_pop($rows);
    }

    public function find(mixed $id, array $columns = ["*"], string $idColumn = "id"): ?object
    {
        $this->where($idColumn, $id);

        return $this->first($columns);
    }
}