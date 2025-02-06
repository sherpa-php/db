<?php

namespace Sherpa\Db\database\exceptions;

use Sherpa\Exceptions\exceptions\SherpaException;

class CannotConnectToDatabaseException extends SherpaException
{
    public function __construct(?Throwable $previous = null)
    {
        $message = "Sherpa cannot connect to database.";

        parent::__construct($message, 1201, $previous);
    }
}