<?php

namespace App\DB;

use App\Exception\DatabaseException;
use PDO;
use PDOException;

// System Path(for php)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// browser path (for css, js, images)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/MediaLibrary-MVC--master');
}

class Database
{
    private static ?PDO $connection = null;
    private static string $host = '127.0.0.1';
    private static string $dbname = 'Database01';
    private static string $user = 'root';
    private static string $pass = '';

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    'mysql:host=' . self::$host . ';dbname=' . self::$dbname . ';charset=utf8',
                    self::$user,
                    self::$pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $exception) {
                throw new DatabaseException(
                    'Database connection failed.',
                    0,
                    $exception
                );
            }
        }

        return self::$connection;
    }
}
