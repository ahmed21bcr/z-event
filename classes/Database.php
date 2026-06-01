<?php

class Database {
    private static ?PDO $instance = null;
    
    private string $host;
    private string $port;
    private string $dbname;
    private string $user;
    private string $password;

    private function __construct() {
        $this->host     = getenv('DB_HOST') ?: 'localhost';
        $this->port     = getenv('DB_PORT') ?: '3306';
        $this->dbname   = getenv('DB_NAME') ?: 'z-event';
        $this->user     = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $db = new Database();
            self::$instance = new PDO(
                "mysql:host={$db->host};port={$db->port};dbname={$db->dbname};charset=utf8mb4",
                $db->user,
                $db->password
            );
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        return self::$instance;
    }
}