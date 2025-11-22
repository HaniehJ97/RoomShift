<?php

namespace App;

class Config
{
    public const DB_HOST = 'mysql';          // from docker-compose
    public const DB_NAME = 'developmentdb';  // from docker-compose
    public const DB_USER = 'developer';      // from docker-compose
    public const DB_PASSWORD = 'secret123';  // from docker-compose

    public static function getDsn(): string
    {
        return 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';
    }
}