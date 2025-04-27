<?php
namespace LorPHP\Core;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dbPath = __DIR__ . '/../../storage/database.sqlite';
            
            // Create the directory if it doesn't exist
            $dirPath = dirname($dbPath);
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
            
            // Ensure we can write to the database file
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0664);
            }
            
            $this->pdo = new \PDO("sqlite:$dbPath");
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->initializeTables();
            
        } catch (\Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new \Exception("Could not connect to database: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeTables() {
        // Create users table if it doesn't exist
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function insert($table, $data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    public function findOne($table, $conditions) {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT * FROM {$table} WHERE {$whereClause} LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function update($table, $data, $conditions) {
        $set = [];
        $params = [];
        foreach ($data as $field => $value) {
            $set[] = "$field = ?";
            $params[] = $value;
        }
        
        $where = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }
        
        $setClause = implode(', ', $set);
        $whereClause = implode(' AND ', $where);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($table, $conditions) {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }
        $whereClause = implode(' AND ', $where);
        
        $sql = "DELETE FROM {$table} WHERE {$whereClause}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
