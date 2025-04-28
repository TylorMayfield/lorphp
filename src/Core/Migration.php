<?php
namespace LorPHP\Core;

abstract class Migration {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    abstract public function up();
    abstract public function down();
    
    protected function createTable($name, $callback) {
        $schema = new Schema($name);
        $callback($schema);
        $this->db->exec($schema->toSql());
    }
    
    protected function dropTable($name) {
        $this->db->exec("DROP TABLE IF EXISTS {$name}");
    }
}
