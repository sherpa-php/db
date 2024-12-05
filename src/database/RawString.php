<?php

namespace Sherpa\Db\database;

class RawString
{
    public private(set) string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}