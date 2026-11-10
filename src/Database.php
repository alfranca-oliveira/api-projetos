<?php

class Database{

    private static ?PDO $pdo = null;

    public static function conexao(): PDO{
        if(self::$pdo === null){
            $config = parse_ini_file(__DIR__.'/../.env');

            self::$pdo= new PDO($config['DB_DSN'], $config['DB_USER'] ?: null, $config['DB_PASS'] ?: null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$pdo;
    }
}