<?php
namespace LorPHP\Core;

abstract class Migration {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->ensureMigrationsTableExists();
    }
    
    abstract public function up();
    abstract public function down();
    
    protected function createTable($name, $callback) {
        $schema = new Schema($name);
        $callback($schema);
        $this->db->exec($schema->toSql());
    }
    
    protected function alterTable($name, $callback) {
        $schema = new Schema($name);
        $schema->setAlterMode();
        $callback($schema);
        $this->db->exec($schema->toSql());
    }
    
    protected function dropTable($name) {
        $this->db->exec("DROP TABLE IF EXISTS {$name}");
    }
    
    private function ensureMigrationsTableExists() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    
    /**
     * Get the last executed batch number
     */
    public function getLastBatch() {
        $result = $this->db->query("SELECT MAX(batch) as batch FROM migrations")->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['batch'] ?? 0);
    }
    
    /**
     * Log that a migration was executed
     */
    public function log($migration, $batch) {
        $this->db->insert('migrations', [
            'migration' => $migration,
            'batch' => $batch
        ]);
    }
    
    /**
     * Remove a migration log
     */
    public function remove($migration) {
        $this->db->exec("DELETE FROM migrations WHERE migration = ?", [$migration]);
    }
}
