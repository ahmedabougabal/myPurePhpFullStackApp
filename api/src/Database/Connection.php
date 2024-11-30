<?php

namespace Kc\CourseCatalog\Database;

class Connection
{
    private static ?\PDO $instance = null;

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $host = getenv('MYSQL_HOST') ?: 'db';
            $dbname = getenv('MYSQL_DATABASE') ?: 'course_catalog';
            $username = getenv('MYSQL_USER') ?: 'test_user';
            $password = getenv('MYSQL_PASSWORD') ?: 'test_password';

            try {
                self::$instance = new \PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_AUTOCOMMIT => false,
                        \PDO::ATTR_PERSISTENT => true,
                        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                        \PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

                // Explicitly set connection attributes
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Log connection error
                error_log('Database Connection Error: ' . $e->getMessage());
                throw $e;
            }
        }

        return self::$instance;
    }

    public static function resetInstance()
    {
        if (self::$instance !== null) {
            // Close the connection
            self::$instance = null;
        }
    }
}