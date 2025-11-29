<?php

namespace App\Framework;

use App\Config;
use PDO;

abstract class Repository
{
    private static ?PDO $connection = null;

    protected function getConnection(): PDO
    {
        if (self::$connection === null) {
            $this->initializeConnection();
        }
        return self::$connection;
    }

    public function __construct()
    {
        if (self::$connection === null) {
            $this->initializeConnection();
        }
    }

    private function initializeConnection(): void
    {
        try {
            $connectionString = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4';
            
            self::$connection = new PDO(
                $connectionString,
                Config::DB_USER,
                Config::DB_PASSWORD
            );
            
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}