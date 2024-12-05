<?php

namespace Sherpa\Db\database;

class Reference
{
    public private(set) string $reference;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }
}