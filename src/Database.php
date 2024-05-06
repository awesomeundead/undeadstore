<?php

namespace Awesomeundead\Undeadstore;

use PDO;

class Database
{
    private static $conection;

    private static $instance;

    private function __construct()
    {
        $config = (require ROOT . '/config.php')['pdo'];
        $dsn = $config['dsn'];
        $username = $config['username'];
        $password = $config['password'];

        self::$conection = new PDO($dsn, $username, $password);
        self::$conection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function connect()
    {
        if(!isset(self::$instance))
        {
            self::$instance = new Database();
        }

        return self::$conection;
    }
}