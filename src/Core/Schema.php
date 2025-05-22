<?php
namespace LorPHP\Core;

class Schema {
    protected $tableName;
    protected $columnGroups = [
        'primary' => [], // Primary key columns
        'regular' => [], // Regular columns
        'constraints' => [] // Foreign keys, unique constraints, etc.
    ];
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
            $this->columnGroups['primary'][] = "id INTEGER PRIMARY KEY AUTOINCREMENT";
        } else if ($type === 'uuid') {
            $this->columnGroups['primary'][] = "id TEXT PRIMARY KEY NOT NULL";
        }
        return $this;
    }

    public function uuid() {
        $this->columnGroups['primary'][] = "id TEXT PRIMARY KEY NOT NULL";
        return $this;
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
        $this->columnGroups['regular'][] = "{$name} INTEGER" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }
    
    public function string($name, $nullable = false) {
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} ADD COLUMN {$name} TEXT" . ($nullable ? '' : ' NOT NULL');
            return $this;
        }
        $this->columnGroups['regular'][] = "{$name} TEXT" . ($nullable ? '' : ' NOT NULL');
        return $this;
    }
    
    public function text($name, $nullable = false) {
        return $this->string($name, $nullable);
    }
    
    public function timestamp($name, $default = null, $nullable = false) {
        $column = "{$name} DATETIME";
        if ($default) {
            $column .= " DEFAULT {$default}";
        }
        if (!$nullable) {
            $column .= " NOT NULL";
        }
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} ADD COLUMN " . $column;
        } else {
            $this->columnGroups['regular'][] = $column;
        }
        return $this;
    }
    
    public function boolean($name, $nullable = false, $default = null) {
        $column = "{$name} INTEGER";
        if ($default !== null) {
            $column .= " DEFAULT " . ($default ? "1" : "0");
        }
        if (!$nullable) {
            $column .= " NOT NULL";
        }
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} ADD COLUMN " . $column;
        } else {
            $this->columnGroups['regular'][] = $column;
        }
        return $this;
    }
    
    public function decimal($name, $precision = 8, $scale = 2, $nullable = false) {
        $column = "{$name} DECIMAL({$precision},{$scale})" . ($nullable ? "" : " NOT NULL");
        if ($this->isAlterTable) {
            $this->alterCommands[] = "ALTER TABLE {$this->tableName} ADD COLUMN " . $column;
        } else {
            $this->columnGroups['regular'][] = $column;
        }
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
        $lastIdx = count($this->columnGroups['regular']) - 1;
        if ($lastIdx >= 0) {
            $this->columnGroups['regular'][$lastIdx] .= " DEFAULT " . (is_string($value) ? "'{$value}'" : $value);
        }
        return $this;
    }
    
    public function nullable() {
        if ($this->isAlterTable) {
            $lastIdx = count($this->alterCommands) - 1;
            if ($lastIdx >= 0) {
                if (stripos($this->alterCommands[$lastIdx], ' NOT NULL') !== false) {
                    $this->alterCommands[$lastIdx] = str_replace(' NOT NULL', '', $this->alterCommands[$lastIdx]);
                }
            }
            return $this;
        }
        $lastIdx = count($this->columnGroups['regular']) - 1;
        if ($lastIdx >= 0) {
            if (stripos($this->columnGroups['regular'][$lastIdx], ' NOT NULL') !== false) {
                $this->columnGroups['regular'][$lastIdx] = str_replace(' NOT NULL', '', $this->columnGroups['regular'][$lastIdx]);
            }
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
        $this->columnGroups['constraints'][] = "FOREIGN KEY ({$column}) REFERENCES {$references}" . 
            ($onDelete ? " ON DELETE {$onDelete}" : "");
        return $this;
    }
    
    public function unique($column) {
        if ($this->isAlterTable) {
            $this->alterCommands[] = "CREATE UNIQUE INDEX {$this->tableName}_{$column}_unique ON {$this->tableName} ({$column})";
        } else {
            $this->columnGroups['constraints'][] = "UNIQUE ({$column})";
        }
        return $this;
    }
    
    public function primary() {
        $lastIdx = count($this->columnGroups['regular']) - 1;
        if ($lastIdx >= 0) {
            if (stripos($this->columnGroups['regular'][$lastIdx], 'PRIMARY KEY') === false) {
                $this->columnGroups['regular'][$lastIdx] .= " PRIMARY KEY";
            }
        }
        return $this;
    }
    
    public function autoIncrement() {
        $lastIdx = count($this->columnGroups['regular']) - 1;
        if ($lastIdx >= 0) {
            if (stripos($this->columnGroups['regular'][$lastIdx], 'AUTOINCREMENT') === false) {
                $this->columnGroups['regular'][$lastIdx] .= " AUTOINCREMENT";
            }
        }
        return $this;
    }
    
    public function toSql() {
        if ($this->isAlterTable) {
            return implode("; ", $this->alterCommands);
        }

        // Combine all column definitions in the correct order
        $columns = array_merge(
            $this->columnGroups['primary'],
            $this->columnGroups['regular'],
            $this->columnGroups['constraints']
        );
        
        return "CREATE TABLE IF NOT EXISTS {$this->tableName} (" . 
            implode(", ", $columns) . 
        ")";
    }
}
