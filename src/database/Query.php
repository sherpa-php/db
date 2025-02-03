<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\JoinType;
use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\enums\OrderType;

class Query
{
    private string $table;
    private array $columns = ["*"];
    private array $joins = [];
    private array $conditions = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $having = [];
    private ?int $limit = null;
    private ?int $offset = null;
    protected array $parameters = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }


    /*
     * ============================================
     *             CREATION STATEMENT
     * ============================================
     */

    /**
     * Creates a new row to set table,
     * using provided data.
     *
     * @param array $data
     * @return object|null Created row if retrieved successfully
     */
    public function create(array $data): ?object
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $this->parameters = array_values($data);

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $this->table,
            implode(", ", $columns),
            implode(", ", $placeholders));

        DB::run($sql, $this->parameters);

        return DB::table($this->table)
                 ->find(DB::lastInsertId());
    }


    /*
     * ============================================
     *              UPDATE STATEMENT
     * ============================================
     */

    public function update(array $data): void
    {
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
     *             SELECTION STATEMENT
     * ============================================
     */

    /**
     * Defines columns to select.
     * <p>
     *     If none column is provided,
     *     query will select all columns ('*').
     * </p>
     *
     * @param array $columns
     * @return Query|DatabaseQuery
     */
    public function select(array $columns = ["*"]): self
    {
        $this->columns = $columns;

        return $this;
    }


    /*
     * ============================================
     *               JOIN STATEMENTS
     * ============================================
     */

    /**
     * Adds a join of type INNER by default.
     *
     * @param string $table
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed|null $value
     * @param JoinType $joinType
     * @return Query|DatabaseQuery
     */
    public function join(string $table,
                         string $column,
                         mixed $operatorOrValue,
                         mixed $value = null,
                         JoinType $joinType = JoinType::INNER): self
    {
        $comparisonOperator = $value === null
            ? '='
            : $operatorOrValue;

        $value = $value ?? $operatorOrValue;

        // TODO: implement * conditions via callback
        $condition = new Condition(
            $column,
            $comparisonOperator,
            $value,
            Operator::AND);

        $this->joins[] = new Join($table, [$condition], $joinType);

        return $this;
    }

    /**
     * Adds an INNER join
     *
     * @param string $table
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed|null $value
     * @return $this
     * @see Query::join()
     */
    public function innerJoin(string $table,
                              string $column,
                              mixed $operatorOrValue,
                              mixed $value = null): self
    {
        self::join($table, $column, $operatorOrValue, $value);

        return $this;
    }

    /**
     * Adds a LEFT join.
     *
     * @param string $table
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed|null $value
     * @return $this
     * @see Query::join()
     */
    public function leftJoin(string $table,
                             string $column,
                             mixed $operatorOrValue,
                             mixed $value = null): self
    {
        self::join(
            $table, $column, $operatorOrValue,
            $value, JoinType::LEFT);

        return $this;
    }

    /**
     * Adds a RIGHT join.
     *
     * @param string $table
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed|null $value
     * @return $this
     * @see Query::join()
     */
    public function rightJoin(string $table,
                              string $column,
                              mixed $operatorOrValue,
                              mixed $value = null): self
    {
        self::join(
            $table, $column, $operatorOrValue,
            $value, JoinType::RIGHT);

        return $this;
    }

    /**
     * Adds a FULL OUTER join.
     *
     * @param string $table
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed|null $value
     * @return $this
     * @see Query::join()
     */
    public function fullJoin(string $table,
                             string $column,
                             mixed $operatorOrValue,
                             mixed $value = null): self
    {
        self::join(
            $table, $column, $operatorOrValue,
            $value, JoinType::FULL);

        return $this;
    }


    /*
     * ============================================
     *               WHERE STATEMENTS
     * ============================================
     */

    /**
     * Adds a condition using AND operator by default.
     *
     * @param string $column
     * @param mixed $operatorOrValue Comparison operator or value
     *                               (shortcut using '=')
     * @param mixed $value Value if shortcut is not used
     * @param Operator $operator Condition operator, used on conditions joining,
     *                           AND operator is used by default
     * @return $this
     */
    public function where(string $column,
                          mixed $operatorOrValue,
                          mixed $value = null,
                          Operator $operator = Operator::AND): self
    {
        $comparisonOperator = $value === null
            ? '='
            : $operatorOrValue;

        $value = $value ?? $operatorOrValue;

        $this->conditions[] = new Condition(
            $column, $comparisonOperator, $value, $operator);

        return $this;
    }

    /**
     * Adds a condition using OR operator.
     *
     * @param string $column
     * @param mixed $operatorOrValue Comparison operator or value
     *                               (shortcut using '=')
     * @param mixed|null $value Value if shortcut is not used
     * @return $this
     * @see Query::where()
     */
    public function orWhere(string $column,
                            mixed $operatorOrValue,
                            mixed $value = null): self
    {
        return self::where(
            $column, $operatorOrValue, $value, Operator::OR);
    }

    /**
     * Adds a raw condition.
     *
     * @param string $raw
     * @param array $parameters
     * @param Operator $operator
     * @return $this
     */
    public function whereRaw(string $raw,
                             array $parameters = [],
                             Operator $operator = Operator::AND): self
    {
        $this->conditions[] = new ConditionRaw($raw, $operator);
        $this->parameters = [...$this->parameters, ...$parameters];

        return $this;
    }

    /**
     * Adds a raw condition using OR operator.
     *
     * @param string $raw
     * @param array $parameters
     * @return $this
     */
    public function orWhereRaw(string $raw,
                               array $parameters = []): self
    {
        return $this->whereRaw(
            $raw,
            $parameters,
            Operator::OR);
    }

    /**
     * Adds an "in array" condition.
     *
     * @param string $column
     * @param array $array
     * @param Operator $operator
     * @return $this
     */
    public function whereIn(string $column,
                            array $array,
                            Operator $operator = Operator::AND): self
    {
        $this->conditions[] = new ConditionInArray(
            $column,
            $array,
            Operator::AND);

        return $this;
    }

    /**
     * Adds an "in array" condition using OR operator.
     *
     * @param string $column
     * @param array $array
     * @param Operator $operator
     * @return $this
     */
    public function orWhereIn(string $column,
                              array $array): self
    {
        return $this->whereIn(
            $column,
            $array,
            Operator::OR);
    }

    /**
     * Adds a LIKE condition.
     *
     * @param string $column
     * @param string $like SQL LIKE expression
     * @param Operator $operator
     * @return $this
     */
    public function whereLike(string $column,
                              string $like,
                              Operator $operator = Operator::AND): self
    {
        $this->conditions[] = new ConditionLike(
            $column,
            $like,
            $operator);

        return $this;
    }

    /**
     * Adds a LIKE condition using OR operator.
     *
     * @param string $column
     * @param string $like SQL LIKE expression
     * @return $this
     */
    public function orWhereLike(string $column,
                                string $like): self
    {
        return $this->whereLike(
            $column,
            $like,
            Operator::OR);
    }

    /**
     * Adds a REGEXP condition.
     *
     * @param string $column
     * @param string $regex
     * @param Operator $operator
     * @return $this
     */
    public function whereRegex(string $column,
                               string $regex,
                               Operator $operator = Operator::AND): self
    {
        $this->conditions[] = new ConditionRegex(
            $column,
            $regex,
            $operator);

        return $this;
    }

    /**
     * Adds a REGEXP condition using OR operator.
     *
     * @param string $column
     * @param string $regex
     * @return $this
     */
    public function orWhereRegex(string $column,
                                 string $regex): self
    {
        return $this->whereRegex(
            $column,
            $regex,
            Operator::OR);
    }

    /**
     * Adds a BETWEEN condition.
     *
     * @param string $column
     * @param string|int|float $min
     * @param string|int|float $max
     * @param Operator $operator
     * @return $this
     */
    public function whereBetween(string $column,
                                 string|int|float $min,
                                 string|int|float $max,
                                 Operator $operator = Operator::AND): self
    {
        $this->conditions[] = new ConditionBetween(
            $column,
            $min,
            $max,
            $operator);

        return $this;
    }

    /**
     * Adds a BETWEEN condition using OR operator.
     *
     * @param string $column
     * @param string|int|float $min
     * @param string|int|float $max
     * @param Operator $operator
     * @return $this
     */
    public function orWhereBetween(string $column,
                                   string|int|float $min,
                                   string|int|float $max,
                                   Operator $operator = Operator::OR): self
    {
        return $this->whereBetween(
            $column,
            $min,
            $max,
            Operator::OR);
    }

    public function whereGroup(callable $group,
                               Operator $operator = Operator::AND): self
    {
        $this->conditions[] = new ConditionGroup(
            $group,
            $operator);

        return $this;
    }


    /*
     * ============================================
     *            GROUP BY STATEMENTS
     * ============================================
     */

    /**
     * Adds a group by statement.
     *
     * @param string ...$columns
     * @return $this
     */
    public function groupBy(string ...$columns): self
    {
        $this->groupBy = $columns;

        return $this;
    }


    /*
     * ============================================
     *             HAVING STATEMENTS
     * ============================================
     */

    /**
     * Adds a having condition using AND operator by default.
     *
     * @param string $column
     * @param mixed $operatorOrValue Comparison operator or value
     *                               (shortcut using '=')
     * @param mixed $value Value if shortcut is not used
     * @param Operator $operator Condition operator, used on conditions joining,
     *                           AND operator is used by default
     * @return $this
     */
    public function having(string $column,
                           mixed $operatorOrValue,
                           mixed $value = null,
                           Operator $operator = Operator::AND): self
    {
        $comparisonOperator = $value === null
            ? '='
            : $operatorOrValue;

        $value = $value ?? $operatorOrValue;

        $this->having[] = new Condition(
            $column, $comparisonOperator, $value, $operator);

        return $this;
    }

    /**
     * Adds a having condition using OR operator.
     *
     * @param string $column
     * @param mixed $operatorOrValue Comparison operator or value
     *                               (shortcut using '=')
     * @param mixed|null $value Value if shortcut is not used
     * @return $this
     * @see Query::having()
     */
    public function orHaving(string $column,
                             mixed $operatorOrValue,
                             mixed $value = null): self
    {
        self::having(
            $column, $operatorOrValue, $value, Operator::OR);

        return $this;
    }


    /*
     * ============================================
     *              ORDER STATEMENTS
     * ============================================
     */

    /**
     * Adds an order statement, ASC by default.
     *
     * @param string $column
     * @param string|OrderType $order
     * @return $this
     */
    public function orderBy(string $column,
                            string|OrderType $order = OrderType::ASC): self
    {
        if (is_string($order))
        {
            $order = OrderType::from($order);
        }

        $this->orderBy[] = new Order($column, $order);

        return $this;
    }

    /**
     * Adds a DESC order statement.
     *
     * @param string $column
     * @return $this
     * @see Query::orderBy()
     */
    public function orderByDesc(string $column): self
    {
        self::orderBy($column, OrderType::DESC);

        return $this;
    }

    /**
     * Adds an order by statement without preparing.
     *
     * @param string $raw Statement's raw
     * @param array $parameters Statement's parameters
     * @return $this
     */
    public function orderByRaw(string $raw, array $parameters = []): self
    {
        $this->orderBy[] = $raw;
        $this->parameters = [...$this->parameters, ...$parameters];

        return $this;
    }


    /*
     * ============================================
     *          LIMIT / OFFSET STATEMENTS
     * ============================================
     */

    /**
     * Adds limit instruction with optional offset.
     *
     * @param int $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(int $limit, ?int $offset = null): self
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Adds offset instruction.
     *
     * @param int $offset
     * @return $this
     * @see Query::limit()
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }


    /*
     * ============================================
     *               SQL STATEMENTS
     * ============================================
     */

    public function sql(): string
    {
        $sql = [
            $this->prepareSelect(),
            $this->prepareFrom(),
        ];

        if (count($this->joins))
        {
            $sql[] = $this->prepareJoin();
        }

        if (count($this->conditions))
        {
            $sql[] = $this->prepareWhere();
        }

        if (count($this->groupBy))
        {
            $sql[] = $this->prepareGroupBy();
        }

        if (count($this->having))
        {
            $sql[] = $this->prepareHaving();
        }

        if (count($this->orderBy))
        {
            $sql[] = $this->prepareOrderBy();
        }

        if ($this->limit !== null)
        {
            $sql[] = $this->prepareLimit();
        }

        if ($this->offset !== null)
        {
            $sql[] = $this->prepareOffset();
        }

        return implode(' ', $sql);
    }

    /**
     * @return string SQL query's SELECT statement
     */
    private function prepareSelect(): string
    {
        $columns = implode(", ", $this->columns);

        return "SELECT $columns";
    }

    /**
     * @return string SQL query's FROM statement
     */
    private function prepareFrom(): string
    {
        return "FROM $this->table";
    }

    /**
     * @return string SQL query's JOIN statements
     */
    private function prepareJoin(): string
    {
        $joins = "";

        foreach ($this->joins as $join)
        {
            if (strlen($joins))
            {
                $joins .= ' ';
            }

            $conditions = $this->prepareConditions($join->conditions);

            $joins .= "{$join->joinType->name} JOIN {$join->table} "
                . "ON $conditions";
        }

        return $joins;
    }

    /**
     * @return string SQL query's WHERE statements
     */
    private function prepareWhere(): string
    {
        return "WHERE {$this->prepareConditions($this->conditions)}";
    }

    /**
     * @return string SQL query's GROUP BY statement
     */
    private function prepareGroupBy(): string
    {
        return "GROUP BY " . implode(", ", $this->groupBy);
    }

    /**
     * @return string SQL query's HAVING statements
     */
    private function prepareHaving(): string
    {
        return "HAVING {$this->prepareConditions($this->having)}";
    }

    /**
     * @return string SQL query's ORDER BY statement
     */
    private function prepareOrderBy(): string
    {
        $statements = [];

        foreach ($this->orderBy as $orderBy)
        {
            if ($orderBy instanceof Order)
            {
                $statements[] = "$orderBy->column {$orderBy->orderType->name}";
            }
            elseif (is_string($orderBy))
            {
                $statements[] = $orderBy;
            }
        }

        return "ORDER BY " . implode(", ", $statements);
    }

    /**
     * @return string SQL query's LIMIT statement
     */
    private function prepareLimit(): string
    {
        return "LIMIT $this->limit";
    }

    /**
     * @return string SQL query's OFFSET statement
     */
    private function prepareOffset(): string
    {
        return "OFFSET $this->offset";
    }

    /**
     * @param array $conditions
     * @return string Imploded where statements string
     */
    private function prepareConditions(array $conditions): string
    {
        $conditionsString = "";

        foreach ($conditions as $condition)
        {
            if ($condition instanceof ConditionGroup)
            {
                $scopeQuery = new ConditionScopeQuery($this->table);
                $condition->methods["group"]($scopeQuery);
                $this->parameters = [
                    ...$this->parameters,
                    ...$scopeQuery->parameters
                ];

                $conditionsString .= "(";
                $conditionsString .= $this->prepareConditions(
                    $scopeQuery->conditions);
                $conditionsString .= ")";
            }
            else
            {
                if (strlen($conditionsString))
                {
                    $conditionsString .= " {$condition->operator->name} ";
                }

                $statement = $condition->statement();
                $conditionsString .= $statement->statement;
                $this->parameters = [
                    ...$this->parameters,
                    ...$statement->parameters
                ];
            }
        }

        return $conditionsString;
    }


    /*
     * ============================================
     *              FETCH STATEMENTS
     * ============================================
     */

    /**
     * Get all SQL rows.
     *
     * @param array $columns
     */
    public function get(array $columns = ["*"])
    { }

    /**
     * Get first SQL row.
     *
     * @param array $columns
     */
    public function first(array $columns = ["*"])
    { }

    /**
     * Get last SQL row.
     *
     * @param array $columns
     */
    public function last(array $columns = ["*"])
    { }

    /**
     * Get SQL row by its id.
     *
     * @param int $id
     * @param array $columns
     * @param string $idColumn Primary Key column name to use, "id" by default
     */
    public function find(int $id, array $columns = ["*"], string $idColumn = "id")
    { }

    /**
     * Get SQL rows count.
     */
    public function count()
    { }
}