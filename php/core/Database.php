<?php

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . Config::get('db_host') . ';port=' . Config::get('db_puerto', '3306')
                . ';dbname=' . Config::get('db_nombre') . ';charset=utf8mb4';
            self::$pdo = new PDO($dsn, Config::get('db_usuario'), Config::get('db_clave'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
