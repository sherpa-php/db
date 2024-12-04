<?php

namespace Sherpa\Db\database;

use PDO;
use PDOException;

/**
 * Database management class.
 */
class DB
{
    private static PDO $pdo;

    /**
     * Attempts to connect to database,
     * using provided credentials.
     *
     * @return bool If connection attempt is successful
     */
    private static function connect(): bool
    {
        $db = self::db();
        $dsn = "{$db['dbms']}:host={$db['host']};port={$db['port']}"
             . ";charset={$db['charset']}";

        try
        {
            self::$pdo = new PDO(
                $dsn,
                $db["user"],
                $db["password"]);
        }
        catch (PDOException $exception)
        {
            return false;
        }

        return true;
    }

    /**
     * SECURITY WARNING: DO NOT SHARE TO CLIENT!
     *
     * @return array All database credentials
     */
    public static function db(): array
    {
        return [
            "dbms" => $_ENV("DB_DBMS"),
            "host" => $_ENV("DB_HOST"),
            "dbname" => $_ENV("DB_NAME"),
            "user" => $_ENV("DB_USER"),
            "password" => $_ENV("DB_PASSWORD"),
        ];
    }
}