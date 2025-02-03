<?php

namespace Sherpa\Db\database;

class DatabaseQuery extends Query
{
    /*
     * ============================================
     *              UPDATE STATEMENT
     * ============================================
     */

    /**
     * Updates row(s).
     *
     * @param array $data
     * @param int|null $id (optional) id of the row to be updated
     */
    public function update(array $data, ?int $id = null): void
    {
        if ($id !== null)
        {
            $this->where("id", $id);
        }

        $columns = array_keys($data);
        $updates = [];

        foreach ($columns as $column)
        {
            $updates[] = "$column = ?";
        }

        $this->parameters = array_values($data);

        $statements = [];

        if (count($this->joins))
        {
            $statements[] = $this->prepareJoin();
        }

        if (count($this->conditions))
        {
            $statements[] = $this->prepareWhere();
        }

        if (count($this->orderBy))
        {
            $statements[] = $this->prepareOrderBy();
        }

        if ($this->limit !== null)
        {
            $statements[] = $this->prepareLimit();
        }

        $sql = sprintf(
            "UPDATE `%s` SET %s %s",
            $this->table,
            implode(", ", $updates),
            implode(' ', $statements));

        DB::run($sql, $this->parameters);
    }


    /*
     * ============================================
     *              DELETE STATEMENT
     * ============================================
     */

    /**
     * Deletes row(s).
     *
     * @param int|null $id (optional) id of the row to be deleted
     */
    public function delete(?int $id = null): void
    {
        if ($id !== null)
        {
            $this->where("id", $id);
        }

        $statements = [];

        if (count($this->joins))
        {
            $statements[] = $this->prepareJoin();
        }

        if (count($this->conditions))
        {
            $statements[] = $this->prepareWhere();
        }

        if (count($this->orderBy))
        {
            $statements[] = $this->prepareOrderBy();
        }

        if ($this->limit !== null)
        {
            $statements[] = $this->prepareLimit();
        }

        $sql = sprintf(
            "DELETE FROM `%s` %s",
            $this->table,
            implode(' ', $statements));

        DB::run($sql, $this->parameters);
    }


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