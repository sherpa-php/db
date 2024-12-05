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
     * @param string $dbname
     * @param string $user
     * @param string $password
     * @return bool If connection attempt is successful
     */
    public static function connect(string $dbms,
                                   string $host,
                                   string|int $port,
                                   string $charset,
                                   string $dbname,
                                   string $user,
                                   string $password): bool
    {
        $dsn = "$dbms:host=$host;port=$port;dbname=$dbname;charset=$charset";

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

    public static function run(string $sql,
                                  array $parameters = []): array
    {
        $pdo = self::$pdo;

        try
        {
            $result = $pdo->prepare($sql);
            $result->execute($parameters);

            return $result->fetchAll();
        }
        catch (PDOException $e)
        {
            return [];
        }
    }

    /**
     * Returns a RawString for separating strings
     * and db references in SQL expressions.
     *
     * @param string $value
     * @return RawString
     */
    public static function string(string $value): RawString
    {
        return new RawString($value);
    }
}