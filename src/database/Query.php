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
            $statements[] = "$orderBy->column {$orderBy->orderType->name}";
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

    private function prepareConditions(array $conditions): string
    {
        $conditionsString = "";

        foreach ($conditions as $condition)
        {
            if (strlen($conditionsString))
            {
                $conditionsString .= " {$condition->operator->name} ";
            }

            if ($condition->value instanceof Reference)
            {
                $value = $condition->value->reference;
            }
            else
            {
                $value = '?';
                $this->parameters[] = $condition->value;
            }

            $conditionsString .= "$condition->column "
                . "$condition->comparisonOperator "
                . "$value";
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