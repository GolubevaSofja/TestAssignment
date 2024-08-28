<?php
// Database.php
namespace classes;
use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct() {
        $config = require __DIR__ . '/../config/config.php';

        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8";
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
