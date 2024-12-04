<?php

namespace Sherpa\Db\database;

class DatabaseQuery
{
    private string $table;
    private array $columns = [];
    private array $conditions = [];
    private array $joins = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $having = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $values = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function select(array|string $columns = "*")
    {
        // TODO
    }
}