<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseFactory
{
    /**
     * @var PDO
     */
    protected static $pdo;
    
    /**
     * Instancia a classe PDO
     *
     * @return PDO
     */
    public static function getInstance()
    {
        if ( !self::$pdo instanceof PDO ) {
            try {

                self::$pdo = new PDO(
                    "mysql:host=" . $_ENV["HOST"] . ";dbname=" . $_ENV["DB_NAME"] . ";", $_ENV["DB_USER"], $_ENV["DB_PASSWD"],
                    [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_CASE => PDO::CASE_NATURAL
                    ]
                );

            } catch ( PDOException $ex ) {
                die("Error {$ex->getMessage()}");
            }
        }
        return self::$pdo;
    }
}