<?php
namespace LorPHP\Core;

class Schema {
    protected $tableName;
    protected $columns = [];
    protected $isAlterTable = false;
    protected $alterCommands = [];
    
    public function __construct($tableName) {
        $this->tableName = $tableName;
    }
    
    public function setAlterMode() {
        $this->isAlterTable = true;
        return $this;
    }
    
    public function id($type = 'integer') {
        if ($type === 'integer') {
            $this->columns[] = "id INTEGER PRIMARY KEY AUTOINCREMENT";
        } else if ($type === 'uuid') {
            $this->columns[] = "id TEXT PRIMARY KEY NOT NULL";
        }
        return $this;
    }

    public function uuid() {
        return $this->id('uuid');
    }
    
    public function timestamps() {
        $this->timestamp('created_at', 'CURRENT_TIMESTAMP');
        $this->timestamp('updated_at', 'CURRENT_TIMESTAMP');
        return $this;
    }
    
    public function integer($name, $nullable = false) {
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} ADD COLUMN {$name} INTEGER" . ($nullable ? '' : ' NOT NULL');
            return $this;
        }
        $this->columns[] = "{$name} INTEGER" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }
    
    public function string($name, $nullable = false) {
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} ADD COLUMN {$name} TEXT" . ($nullable ? '' : ' NOT NULL');
            return $this;
        }
        $this->columns[] = "{$name} TEXT" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }
    
    public function timestamp($name, $default = null, $nullable = false) {
        $column = "{$name} DATETIME";
        if ($default) {
            $column .= " DEFAULT {$default}";
        }
        if (!$nullable) {
            $column .= " NOT NULL";
        }
        $this->columns[] = $column;
        return $this;
    }
    
    public function boolean($name, $default = null) {
        $column = "{$name} INTEGER";
        if ($default !== null) {
            $column .= " DEFAULT " . ($default ? "1" : "0");
        }
        $column .= " NOT NULL";
        $this->columns[] = $column;
        return $this;
    }
    
    public function decimal($name, $precision = 8, $scale = 2) {
        $column = "{$name} DECIMAL({$precision},{$scale})";
        $column .= " NOT NULL";
        $this->columns[] = $column;
        return $this;
    }

    public function default($value) {
        if ($this->isAlterTable) {
            $lastIdx = count($this->alterCommands) - 1;
            if ($lastIdx >= 0) {
                $this->alterCommands[$lastIdx] .= " DEFAULT " . (is_string($value) ? "'{$value}'" : $value);
            }
            return $this;
        }
        $lastIdx = count($this->columns) - 1;
        if ($lastIdx >= 0) {
            $this->columns[$lastIdx] .= " DEFAULT " . (is_string($value) ? "'{$value}'" : $value);
        }
        return $this;
    }
    
    public function nullable() {
        if ($this->isAlterTable) {
            $lastIdx = count($this->alterCommands) - 1;
            if ($lastIdx >= 0) {
                $this->alterCommands[$lastIdx] = str_replace(' NOT NULL', '', $this->alterCommands[$lastIdx]);
            }
            return $this;
        }
        $lastIdx = count($this->columns) - 1;
        if ($lastIdx >= 0) {
            $this->columns[$lastIdx] = str_replace(' NOT NULL', '', $this->columns[$lastIdx]);
        }
        return $this;
    }
    
    public function dropColumn($name) {
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} DROP COLUMN {$name}";
        }
        return $this;
    }
    
    public function foreignKey($column, $references, $onDelete = null) {
        $this->columns[] = "FOREIGN KEY ({$column}) REFERENCES {$references}" . 
            ($onDelete ? " ON DELETE {$onDelete}" : "");
        return $this;
    }
    
    public function unique($column) {
        $this->columns[] = "UNIQUE ({$column})";
        return $this;
    }
    
    public function toSql() {
        if ($this->isAlterTable) {
            return implode("; ", $this->alterCommands);
        }
        return "CREATE TABLE IF NOT EXISTS {$this->tableName} (" . 
            implode(", ", $this->columns) . 
        ")";
    }
}
