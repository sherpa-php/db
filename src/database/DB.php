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
     * @param string $dbms
     * @param string $host
     * @param string|int $port
     * @param string $charset
     * @param string $user
     * @param string $password
     * @return bool If connection attempt is successful
     */
    public static function connect(string $dbms,
                                    string $host,
                                    string|int $port,
                                    string $charset,
                                    string $user,
                                    string $password): bool
    {
        $db = self::db();
        $dsn = "$dbms:host=$host;port=$port;charset=$charset";

        try
        {
            self::$pdo = new PDO(
                $dsn,
                $user,
                $password);
        }
        catch (PDOException $exception)
        {
            return false;
        }

        return true;
    }

    /**
     * Create a new DatabaseQuery object.
     *
     * @param string $table
     * @return DatabaseQuery
     */
    public static function table(string $table): DatabaseQuery
    {
        return new DatabaseQuery($table);
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