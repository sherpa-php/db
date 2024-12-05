<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\JoinType;
use Sherpa\Db\database\enums\Operator;

class DatabaseQuery
{
    private string $table;
    private array $columns = [];
    private array $joins = [];
    private array $conditions = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private array $having = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Defines columns to select.
     * <p>
     *     If none column is provided,
     *     query will select all columns ('*').
     * </p>
     *
     * @param array|string $columns
     * @return DatabaseQuery
     */
    public function select(array|string $columns = "*"): self
    {
        $this->columns = is_string($columns)
            ? [$columns]
            : $columns;

        return $this;
    }

    /**
     * Adds a join of type INNER by default.
     *
     * @param string $table
     * @param string $column
     * @param mixed $operatorOrValue
     * @param mixed|null $value
     * @param JoinType $joinType
     * @return $this
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
     */
    public function orWhere(string $column,
                            mixed $operatorOrValue,
                            mixed $value = null): self
    {
        return self::where(
            $column, $operatorOrValue, $value, Operator::OR);
    }

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
     */
    public function orHaving(string $column,
                             mixed $operatorOrValue,
                             mixed $value = null): self
    {
        self::having(
            $column, $operatorOrValue, $value, Operator::OR);

        return $this;
    }

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
}