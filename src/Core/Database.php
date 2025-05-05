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
                $this->pdo = new \PDO("sqlite:$dbPath");
                $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->initializeTables();
            } else {
                $this->pdo = new \PDO("sqlite:$dbPath");
                $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->ensureMigrationsTableExists();
            }
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

    private function ensureMigrationsTableExists() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function initializeTables() {
        $this->ensureMigrationsTableExists();
        $this->runMigrations();
    }
    
    private function runMigrations() {
        try {
            // Get already run migrations
            $stmt = $this->pdo->query("SELECT migration FROM migrations");
            $ranMigrations = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            // Get all migration files
            $migrationsDir = __DIR__ . '/../../database/migrations';
            if (!file_exists($migrationsDir)) {
                mkdir($migrationsDir, 0755, true);
            }
            
            $files = glob($migrationsDir . '/*.php');
            sort($files); // Ensure migrations run in order
            
            if (empty($files)) {
                error_log("No migration files found in {$migrationsDir}");
                return;
            }
            
            $batch = $this->getNextBatchNumber();
            
            foreach ($files as $file) {
                $migrationName = basename($file);
                
                // Skip if migration already ran
                if (in_array($migrationName, $ranMigrations)) {
                    continue;
                }
                
                // Include and run migration
                require_once $file;
                // Convert filename like "001_create_organizations_table" to "CreateOrganizationsTable"
                $baseName = pathinfo($file, PATHINFO_FILENAME);
                $parts = explode('_', $baseName);
                array_shift($parts); // Remove the numeric prefix
                $className = 'LorPHP\\Database\\Migrations\\' . implode('', array_map('ucfirst', $parts));
                $migration = new $className();
                
                $this->pdo->beginTransaction();
                try {
                    $migration->up();
                    $this->logMigration($migrationName, $batch);
                    $this->pdo->commit();
                    error_log("Ran migration: {$migrationName}");
                } catch (\Exception $e) {
                    $this->pdo->rollBack();
                    error_log("Migration failed: {$migrationName}. Error: " . $e->getMessage());
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            error_log("Error running migrations: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function getNextBatchNumber() {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        return (int)$stmt->fetchColumn() + 1;
    }
    
    private function logMigration($migration, $batch) {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migration, $batch]);
    }
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    public function commit() {
        return $this->pdo->commit();
    }
    public function rollBack() {
        return $this->pdo->rollBack();
    }
    public function getPdo() {
        return $this->pdo;
    }
    public function getAll($table, $conditions = [], $orderBy = null) {
        $where = [];
        $params = [];
        $limit = '';
        
        foreach ($conditions as $field => $value) {
            if ($field === 'limit') {
                $limit = " LIMIT " . (int)$value;
                continue;
            }
            $where[] = "$field = ?";
            $params[] = $value;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        if ($orderBy) {
            $orderByClause = "ORDER BY {$orderBy}";
        } else {
            $orderByClause = '';
        }
        
        $sql = "SELECT * FROM {$table} {$whereClause} {$orderByClause}{$limit}";
        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function get($table, $id) {
        $sql = "SELECT * FROM {$table} WHERE id = ?";
        $stmt = $this->query($sql, [$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getByField($table, $field, $value) {
        $sql = "SELECT * FROM {$table} WHERE {$field} = ?";
        $stmt = $this->query($sql, [$value]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getByFields($table, $conditions) {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT * FROM {$table} WHERE {$whereClause}";
        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getCount($table, $conditions = []) {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) FROM {$table} {$whereClause}";
        return (int)$this->query($sql, $params)->fetchColumn();
    }
    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }
    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    public function fetch($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function fetchAll($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function fetchColumn($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    public function fetchObject($sql, $params = [], $className = 'stdClass') {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchObject($className);
    }
    public function fetchAllObjects($sql, $params = [], $className = 'stdClass') {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, $className);
    }
    public function fetchColumnAll($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    public function fetchColumnAllObjects($sql, $params = [], $className = 'stdClass') {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_COLUMN, $className);
    }
    public function fetchColumnAllAssociative($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_COLUMN);
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $params = is_array($params[0] ?? null) ? $params[0] : $params;
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Convert value to appropriate SQLite type
     */
    private function convertValueForSQLite($value, $type = null) {
        if ($value === null) {
            return null;
        }
        
        // If type is explicitly provided, use it
        if ($type !== null) {
            switch ($type) {
                case 'INTEGER':
                case 'int':
                    return (int)$value;
                case 'TEXT':
                case 'string':
                    return (string)$value;
                case 'REAL':
                case 'float':
                    return (float)$value;
                default:
                    return $value;
            }
        }
        
        // Otherwise infer type
        if (is_int($value)) {
            return (int)$value;
        } elseif (is_float($value)) {
            return (float)$value;
        } else {
            return (string)$value;
        }
    }

    public function insert($table, $data) {
        // Get table info to determine column types
        $tableInfo = $this->pdo->query("PRAGMA table_info(" . $table . ")")->fetchAll(\PDO::FETCH_ASSOC);
        $columnTypes = [];
        foreach ($tableInfo as $column) {
            $columnTypes[$column['name']] = $column['type'];
        }
        
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        
        // Convert values based on column types
        $values = [];
        foreach ($data as $field => $value) {
            $type = $columnTypes[$field] ?? null;
            $values[] = $this->convertValueForSQLite($value, $type);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $this->pdo->lastInsertId();
    }

    /**
     * Find a single record by conditions
     */
    public function findOne($table, $conditions) {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $params[] = $value;
        }
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT * FROM {$table} WHERE {$whereClause} LIMIT 1";
        error_log("[DB Debug] Running query: " . $sql . " with params: " . json_encode($params));
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function update($table, $data, $conditions) {
        // Get table info to determine column types
        $tableInfo = $this->pdo->query("PRAGMA table_info(" . $table . ")")->fetchAll(\PDO::FETCH_ASSOC);
        $columnTypes = [];
        foreach ($tableInfo as $column) {
            $columnTypes[$column['name']] = $column['type'];
        }
        
        $set = [];
        $params = [];
        foreach ($data as $field => $value) {
            $set[] = "$field = ?";
            $type = $columnTypes[$field] ?? null;
            $params[] = $this->convertValueForSQLite($value, $type);
        }
        
        foreach ($conditions as $field => $value) {
            $where[] = "$field = ?";
            $type = $columnTypes[$field] ?? null;
            $params[] = $this->convertValueForSQLite($value, $type);
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

    /**
     * Execute an SQL statement and return the number of affected rows
     * 
     * @param string $sql The SQL statement to execute
     * @return int The number of affected rows
     */
    public function exec(string $sql): int {
        return $this->pdo->exec($sql);
    }
}
