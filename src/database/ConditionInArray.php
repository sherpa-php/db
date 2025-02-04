<?php

namespace Sherpa\Db\database;

use Sherpa\Db\database\enums\Operator;
use Sherpa\Db\database\structure\ConditionStatementResponse;

class ConditionInArray implements ConditionInterface
{
    public private(set) string $column;
    public private(set) array $array;
    public private(set) Operator $operator;

    public function __construct(string $column,
                                array $array,
                                Operator $operator)
    {
        $this->column = $column;
        $this->array = $array;
        $this->operator = $operator;
    }

    public function statement(): ConditionStatementResponse
    {
        if (!count($this->array))
        {
            $statement = "1 = 0";

            return new ConditionStatementResponse($statement);
        }

        $parameters = [];

        $preparedArray = array_map(
            function ($value) use (&$parameters)
            {
                if ($value instanceof Reference)
                {
                    return $value->reference;
                }
                else
                {
                    $parameters[] = $value;

                    return '?';
                }
            },
            $this->array);

        $implodedArray = implode(
            ", ",
            $preparedArray);

        $statement = "$this->column IN ($implodedArray)";

        return new ConditionStatementResponse(
            $statement,
            $parameters);
    }
}