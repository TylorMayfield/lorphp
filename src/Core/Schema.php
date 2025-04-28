<?php
namespace LorPHP\Core;

class Schema {
    protected $name;
    protected $columns = [];
    protected $foreignKeys = [];
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function id() {
        $this->columns[] = "id INTEGER PRIMARY KEY AUTOINCREMENT";
        return $this;
    }
    
    public function string($name, $nullable = false) {
        $this->columns[] = "{$name} TEXT" . ($nullable ? "" : " NOT NULL");
        return $this;
    }
    
    public function integer($name, $nullable = false) {
        $this->columns[] = "{$name} INTEGER" . ($nullable ? "" : " NOT NULL");
        return $this;
    }
    
    public function timestamp($name, $default = null) {
        if ($default === 'CURRENT_TIMESTAMP') {
            $this->columns[] = "{$name} DATETIME DEFAULT CURRENT_TIMESTAMP";
        } else {
            $this->columns[] = "{$name} DATETIME" . ($default ? " DEFAULT {$default}" : "");
        }
        return $this;
    }
    
    public function foreignKey($column, $reference, $onDelete = null) {
        $constraint = "FOREIGN KEY ({$column}) REFERENCES {$reference}";
        if ($onDelete) {
            $constraint .= " ON DELETE {$onDelete}";
        }
        $this->foreignKeys[] = $constraint;
        return $this;
    }
    
    public function unique($column) {
        $this->columns[] = "UNIQUE({$column})";
        return $this;
    }
    
    public function toSql() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->name} (\n";
        $sql .= implode(",\n", $this->columns);
        
        if (!empty($this->foreignKeys)) {
            $sql .= ",\n" . implode(",\n", $this->foreignKeys);
        }
        
        $sql .= "\n)";
        
        return $sql;
    }
}
