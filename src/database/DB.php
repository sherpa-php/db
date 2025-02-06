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
                                   string $password): void
    {
        $dsn = "$dbms:host=$host;port=$port;dbname=$dbname;charset=$charset";

        self::$pdo = new PDO(
            $dsn,
            $user,
            $password);
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
     * Run SQL query.
     *
     * @param string $sql
     * @param array $parameters
     * @return array Query rows
     */
    public static function run(string $sql,
                               array $parameters = []): array
    {
        $pdo = self::$pdo;

        try
        {
            $result = $pdo->prepare($sql);
            $result->execute($parameters);

            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            if (strtolower($_ENV["MODE"]) !== "dev")
            {
                if (ob_get_length())
                {
                    ob_clean();
                }

                http_response_code(500);
                exit;
            }
            else
            {
                throw $e;
            }
        }
    }

    /**
     * Returns a Reference for separating strings
     * and db references in SQL expressions.
     *
     * @param string $ref
     * @return Reference
     */
    public static function ref(string $ref): Reference
    {
        return new Reference($ref);
    }

    /**
     * @return string Last inserted ID using PDO native method
     */
    public static function lastInsertId(): string
    {
        return self::$pdo->lastInsertId();
    }
}